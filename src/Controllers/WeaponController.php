<?php

class WeaponController
{
    private $weaponRepo;
    private $storeRepo;

    public function __construct()
    {
        $this->weaponRepo = new WeaponRepository();
        $this->storeRepo = new StoreRepository();
    }

    public function index(): void
    {
        // Get query parameters
        $params = $_GET; // Contains sort_by, sort_dir, page, per_page, search, type, caliber, store_id, etc.
        
        // Get weapons with pagination and filtering
        $result = $this->weaponRepo->findAll($params);
        
        // Get filter options for dropdowns
        $filterOptions = $this->weaponRepo->getFilterOptions();

        render('weapons/list', [
            'title' => 'All Weapons',
            'weapons' => $result['data'],
            'pagination' => [
                'current_page' => $result['page'],
                'total_pages' => $result['total_pages'], 
                'per_page' => $result['per_page'],
                'total' => $result['total'],
                'has_next' => $result['has_next'],
                'has_prev' => $result['has_prev']
            ],
            'sorting' => [
                'sort_by' => $result['sort_by'],
                'sort_dir' => $result['sort_dir']
            ],
            'filters' => $result['filters'],
            'filter_options' => $filterOptions
        ]);
    }


    
    public function create(): void
    {
        $stores = $this->storeRepo->findAllWithoutPagination();
        render('weapons/form', [
            'title' => 'Add New Weapon',
            'weapon' => [],
            'stores' => $stores,
            'action' => '/weapons/store',
            'buttonText' => 'Create Weapon'
        ]);
    }

    
    public function store(): void
    {
        $data = $_POST;
         // Validate the input data
        $validator = new WeaponValidator($_POST);
        
        if (!$validator->validate()) {
            // Validation failed - create a combined error message
            $errors = $validator->getErrors();
            $errorMessage = "Please fix the following errors:\n";
            foreach ($errors as $field => $message) {
                $errorMessage .= "• " . ucfirst(str_replace('_', ' ', $field)) . ": " . $message . "\n";
            }
            setFlashMessage('error', $errorMessage);
            header('Location: /weapons/create');
            exit();
        }


        try {
            $this->weaponRepo->save($data);
            setFlashMessage('success', 'Weapon created successfully!');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                setFlashMessage('error', 'A weapon with that serial number already exists.');
            } else {
                setFlashMessage('error', 'Error creating weapon: ' . $e->getMessage());
            }
            header('Location: /weapons/create');
            exit();
        }

        header('Location: /weapons/list');
        exit();
    }

   
    public function edit(int $id): void
    {
        $weapon = $this->weaponRepo->findById($id);

        if (!$weapon) {
            http_response_code(404);
            render('errors/404');
            return;
        }

        $stores = $this->storeRepo->findAllWithoutPagination();

        render('weapons/form', [
            'title' => 'Edit Weapon',
            'weapon' => $weapon,
            'stores' => $stores,
            'action' => '/weapons/update/' . $id,
            'buttonText' => 'Update Weapon'
        ]);
    }

  
    public function update(int $id): void
    {
        $weapon = $this->weaponRepo->findById($id);
        if (!$weapon) {
            http_response_code(404);
            render('errors/404');
            return;
        }

        $data = $_POST;
        // @TODO: Add validation logic here

        try {
            $this->weaponRepo->update($data, $id);
            setFlashMessage('success', 'Weapon updated successfully!');
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                setFlashMessage('error', 'A weapon with that serial number already exists.');
            } else {
                setFlashMessage('error', 'Error updating weapon: ' . $e->getMessage());
            }
            header('Location: /weapons/edit/' . $id);
            exit();
        }

        header('Location: /weapons/list');
        exit();
    }

 
    public function delete(int $id): void
    {
        $weapon = $this->weaponRepo->findById($id);
        if (!$weapon) {
            http_response_code(404);
            setFlashMessage('error', 'Weapon not found.');
            header('Location: /weapons/list');
            exit();
        }

        try {
            if ($this->weaponRepo->delete($id)) {
                setFlashMessage('success', 'Weapon deleted successfully!');
            } else {
                setFlashMessage('error', 'Failed to delete the weapon.');
            }
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error deleting weapon: ' . $e->getMessage());
        }

        header('Location: /weapons/list');
        exit();
    }

   
    public function pdf(int $id): void
    {
        $weapon = $this->weaponRepo->findById($id);
        if (!$weapon) {
            http_response_code(404);
            die("Weapon not found");
        }

        // Get store information
        $store = $this->storeRepo->findById($weapon['store_id']);
        if (!$store) {
            http_response_code(404);
            die("Store not found");
        }

        // Generate PDF
        $pdfGenerator = new WeaponPDFGenerator();
        $pdfGenerator->generateWeaponPDF($weapon, $store);
    }

    /**
     * Bulk PDF export - generates a zip file containing individual weapon PDFs
     */
    public function bulkPdf(): void
    {
        // Check if weapon IDs were provided
        if (empty($_POST['weapon_ids']) || !is_array($_POST['weapon_ids'])) {
            setFlashMessage('error', 'No weapons selected for export.');
            header('Location: /weapons/list');
            exit();
        }

        $weaponIds = array_map('intval', $_POST['weapon_ids']);
        
        // Validate that we have valid IDs
        if (empty($weaponIds)) {
            setFlashMessage('error', 'Invalid weapon selection.');
            header('Location: /weapons/list');
            exit();
        }

        try {
            // Create temporary directory for PDFs
            $tempDir = sys_get_temp_dir() . '/weapon_pdfs_' . uniqid();
            if (!mkdir($tempDir, 0755, true)) {
                throw new Exception('Failed to create temporary directory');
            }

            $generatedFiles = [];
            $failedWeapons = [];

            foreach ($weaponIds as $weaponId) {
                try {
                    // Get weapon data
                    $weapon = $this->weaponRepo->findById($weaponId);
                    if (!$weapon) {
                        $failedWeapons[] = "Weapon ID {$weaponId} not found";
                        continue;
                    }

                    // Get store data
                    $store = $this->storeRepo->findById($weapon['store_id']);
                    if (!$store) {
                        $failedWeapons[] = "Store not found for weapon: {$weapon['name']}";
                        continue;
                    }

                    // Generate filename (sanitize for filesystem)
                    $filename = $this->sanitizeFilename($weapon['name'] . '_' . $weapon['serial_number']) . '.pdf';
                    $filepath = $tempDir . '/' . $filename;

                    // Create a new PDF generator instance for each weapon
                    $pdfGenerator = new WeaponPDFGenerator();
                    
                    // Generate PDF content using the working method from your WeaponPDFGenerator
                    $pdfContent = $pdfGenerator->generateWeaponPDFContent($weapon, $store);
                    
                    if (file_put_contents($filepath, $pdfContent) === false) {
                        $failedWeapons[] = "Failed to generate PDF for weapon: {$weapon['name']}";
                        continue;
                    }

                    $generatedFiles[] = [
                        'path' => $filepath,
                        'name' => $filename
                    ];

                } catch (Exception $e) {
                    $failedWeapons[] = "Error processing weapon ID {$weaponId}: " . $e->getMessage();
                    continue;
                }
            }

            // Check if we have any files to zip
            if (empty($generatedFiles)) {
                $this->cleanupTempDir($tempDir);
                setFlashMessage('error', 'No PDFs could be generated. ' . implode(', ', $failedWeapons));
                header('Location: /weapons/list');
                exit();
            }

            // Create ZIP file
            $zipFilename = 'weapons_export_' . date('Y-m-d_H-i-s') . '.zip';
            $zipPath = $tempDir . '/' . $zipFilename;

            if (!class_exists('ZipArchive')) {
                throw new Exception('ZipArchive extension is not available');
            }

            $zip = new ZipArchive();
            $result = $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            
            if ($result !== TRUE) {
                $this->cleanupTempDir($tempDir);
                throw new Exception('Cannot create ZIP file. Error code: ' . $result);
            }

            // Add files to ZIP
            foreach ($generatedFiles as $file) {
                if (!file_exists($file['path'])) {
                    $failedWeapons[] = "File not found: {$file['name']}";
                    continue;
                }
                
                if (!$zip->addFile($file['path'], $file['name'])) {
                    $failedWeapons[] = "Failed to add {$file['name']} to ZIP";
                }
            }

            $zip->close();

            // Check if ZIP was created successfully
            if (!file_exists($zipPath) || filesize($zipPath) == 0) {
                $this->cleanupTempDir($tempDir);
                throw new Exception('ZIP file was not created successfully or is empty');
            }

            // Send ZIP file to browser
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
            header('Content-Length: ' . filesize($zipPath));
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            // Disable output buffering to prevent memory issues
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Output the file
            $handle = fopen($zipPath, 'rb');
            if ($handle) {
                while (!feof($handle)) {
                    echo fread($handle, 8192);
                    flush();
                }
                fclose($handle);
            } else {
                throw new Exception('Could not read ZIP file');
            }

            // Cleanup temporary files
            $this->cleanupTempDir($tempDir);

            exit();

        } catch (Exception $e) {
            // Cleanup on error
            if (isset($tempDir)) {
                $this->cleanupTempDir($tempDir);
            }
            
            setFlashMessage('error', 'Error generating bulk PDF export: ' . $e->getMessage());
            header('Location: /weapons/list');
            exit();
        }
    }

     /**
     * Sanitize filename for filesystem compatibility
     */
    private function sanitizeFilename(string $filename): string
    {
        // Remove or replace invalid characters
        $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $filename);
        // Remove multiple underscores
        $filename = preg_replace('/_+/', '_', $filename);
        // Trim underscores from ends
        $filename = trim($filename, '_');
        // Limit length
        return substr($filename, 0, 100);
    }

    /**
     * Clean up temporary directory and all its contents
     */
    private function cleanupTempDir(string $tempDir): void
    {
        if (!is_dir($tempDir)) {
            return;
        }

        try {
            $files = glob($tempDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($tempDir);
        } catch (Exception $e) {
            // Log error but don't throw - this is cleanup
            error_log("Failed to cleanup temp directory {$tempDir}: " . $e->getMessage());
        }
    }
}


