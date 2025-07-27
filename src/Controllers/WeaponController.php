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
     * API endpoint for store autocomplete
     */
    public function storeAutocomplete(): void
    {
        header('Content-Type: application/json');
        
        if (!isset($_GET['query']) || strlen(trim($_GET['query'])) < 2) {
            echo json_encode([]);
            exit();
        }
        
        $query = trim($_GET['query']);
        $limit = min(10, (int)($_GET['limit'] ?? 10));
        
        try {
            $stores = $this->storeRepo->searchStores($query, $limit);
            echo json_encode($stores);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Search failed']);
        }
        exit();
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

    public function exportCsv(): void
    {
        try {
            // Get all weapons with store information (no pagination for export)
            $params = $_GET;
            $params['per_page'] = 999999;
            $result = $this->weaponRepo->findAll($params);
            $weapons = $result['data'];

            if (empty($weapons)) {
                setFlashMessage('warning', 'No weapons found to export.');
                header('Location: /weapons/list');
                exit();
            }

            // Clean output buffer before sending headers
            if (ob_get_length()) {
                ob_end_clean();
            }

            // Set CSV headers
            $filename = 'weapons_export_' . date('Y-m-d_H-i-s') . '.csv';
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-store, no-cache');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Open output stream
            $output = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fwrite($output, "\xEF\xBB\xBF");

            // Column headers
            $headers = [
                'ID',
                'Name',
                'Type',
                'Caliber',
                'Serial Number',
                'Price',
                'Store Name',
                'Store ID',
                'In Stock',
                'Status',
                'Created At',
                'Updated At'
            ];
            fputcsv($output, $headers, ',', '"', '\\'); // include escape char

            // Write data
            foreach ($weapons as $weapon) {
                $row = [
                    $weapon['id'] ?? '',
                    $weapon['name'] ?? '',
                    $weapon['type'] ?? '',
                    $weapon['caliber'] ?? '',
                    $weapon['serial_number'] ?? '',
                    number_format((float)($weapon['price'] ?? 0), 2, '.', ''),
                    $weapon['store_name'] ?? '',
                    $weapon['store_id'] ?? '',
                    isset($weapon['in_stock']) ? ($weapon['in_stock'] ? 'Yes' : 'No') : '',
                    $weapon['status'] ?? '',
                    $weapon['created_at'] ?? '',
                    $weapon['updated_at'] ?? ''
                ];

                fputcsv($output, $row, ',', '"', '\\'); // include escape char
            }

            fclose($output);
            exit();

        } catch (Exception $e) {
            if (ob_get_length()) {
                ob_end_clean();
            }

            setFlashMessage('error', 'Error exporting CSV: ' . $e->getMessage());
            header('Location: /weapons/list');
            exit();
        }
    }


    /**
     * Show CSV import form
     */
    public function importCsvForm(): void
    {
        $stores = $this->storeRepo->findAllWithoutPagination();
        
        render('weapons/csv_import', [
            'title' => 'Import Weapons from CSV',
            'stores' => $stores
        ]);
    }

   /**
 * Process CSV import with enhanced validation
 */
public function importCsv(): void
{
    try {
        // Validate file upload
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Please select a valid CSV file to upload.');
        }

        $file = $_FILES['csv_file'];
        
        // Validate file type and size
        $this->validateUploadedFile($file);

        // Read and parse CSV
        $csvData = $this->parseCsvFile($file['tmp_name']);
        
        if (empty($csvData)) {
            throw new Exception('CSV file is empty or could not be parsed.');
        }

        // Get import options
        $skipFirstRow = isset($_POST['skip_first_row']) && $_POST['skip_first_row'] === '1';
        $updateExisting = isset($_POST['update_existing']) && $_POST['update_existing'] === '1';
        $defaultStoreId = !empty($_POST['default_store_id']) ? (int)$_POST['default_store_id'] : null;

        // Initialize CSV validator
        $stores = $this->storeRepo->findAllWithoutPagination();
        $validStoreIds = array_column($stores, 'id');
        $validator = new CsvWeaponValidator($validStoreIds);

        // Validate CSV structure
        $structureValidation = $validator->validateCsvStructure($csvData, $skipFirstRow);
        if (!$structureValidation['valid']) {
            throw new Exception('CSV structure error: ' . implode(', ', $structureValidation['errors']));
        }

        // Remove header row if needed
        $dataRows = $csvData;
        if ($skipFirstRow && count($dataRows) > 0) {
            array_shift($dataRows);
        }

        if (empty($dataRows)) {
            throw new Exception('No data rows found in CSV file.');
        }

        // Process the import with enhanced validation
        $importResults = $this->processEnhancedWeaponImport($dataRows, $validator, $updateExisting, $defaultStoreId);

        // Generate success/error message
        $this->setImportResultMessage($importResults);

    } catch (Exception $e) {
        setFlashMessage('error', 'Import failed: ' . $e->getMessage());
    }

    header('Location: /weapons/list');
    exit();
}

/**
 * Validate uploaded file
 */
private function validateUploadedFile(array $file): void
{
    // Validate file type
    $allowedTypes = ['text/csv', 'application/csv', 'text/plain'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($mimeType, $allowedTypes) && $extension !== 'csv') {
        throw new Exception('Invalid file type. Please upload a CSV file.');
    }

    // Validate file size (10MB max)
    if ($file['size'] > 10 * 1024 * 1024) {
        throw new Exception('File too large. Maximum size is 10MB.');
    }

    // Check if file is readable
    if (!is_readable($file['tmp_name'])) {
        throw new Exception('Unable to read the uploaded file.');
    }
}

/**
 * Parse CSV file with proper encoding and delimiter detection
 */
private function parseCsvFile(string $filePath): array
{
    try {
        // Read file content
        $content = file_get_contents($filePath);
        
        if ($content === false) {
            throw new Exception('Unable to read CSV file.');
        }

        // Remove UTF-8 BOM if present
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }

        // Auto-detect line endings
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        
        // Split into lines
        $lines = explode("\n", $content);
        
        // Remove empty lines
        $lines = array_filter($lines, function($line) {
            return trim($line) !== '';
        });

        if (empty($lines)) {
            throw new Exception('CSV file contains no data.');
        }

        // Auto-detect delimiter
        $delimiter = $this->detectCsvDelimiter($lines[0]);
        
        $csvData = [];
        
        foreach ($lines as $lineNumber => $line) {
            try {
                // Parse CSV line with proper parameters
                $row = str_getcsv($line, $delimiter, '"', "\\");
                
                // Skip empty rows
                if (!empty(array_filter($row, function($cell) {
                    return trim($cell) !== '';
                }))) {
                    $csvData[] = $row;
                }
                
            } catch (Exception $e) {
                throw new Exception("Error parsing line " . ($lineNumber + 1) . ": " . $e->getMessage());
            }
        }

        return $csvData;
        
    } catch (Exception $e) {
        throw new Exception('CSV parsing failed: ' . $e->getMessage());
    }
}

/**
 * Auto-detect CSV delimiter
 */
private function detectCsvDelimiter(string $firstLine): string
{
    $delimiters = [',', ';', "\t", '|'];
    $delimiter = ','; // default
    $maxCount = 0;
    
    foreach ($delimiters as $d) {
        $count = substr_count($firstLine, $d);
        if ($count > $maxCount) {
            $maxCount = $count;
            $delimiter = $d;
        }
    }
    
    return $delimiter;
}

/**
 * Map CSV row to weapon data structure
 */
private function mapCsvRowToWeaponData(array $row, ?int $defaultStoreId): array
{
    // Expected CSV columns (adjust based on your CSV structure)
    $expectedColumns = [
        'name',
        'type', 
        'category',
        'serial_number',
        'caliber',
        'manufacturer',
        'model',
        'barrel_length',
        'overall_length',
        'weight',
        'capacity',
        'action_type',
        'finish',
        'stock_material',
        'sights',
        'safety_features',
        'accessories',
        'condition',
        'purchase_date',
        'purchase_price',
        'current_value',
        'store_id',
        'notes'
    ];
    
    $weaponData = [];
    
    // Map row values to expected columns
    foreach ($expectedColumns as $index => $column) {
        $weaponData[$column] = isset($row[$index]) ? trim($row[$index]) : '';
    }
    
    // Use default store if no store_id provided or invalid
    if (empty($weaponData['store_id']) || !is_numeric($weaponData['store_id'])) {
        $weaponData['store_id'] = $defaultStoreId;
    } else {
        $weaponData['store_id'] = (int)$weaponData['store_id'];
    }
    
    // Convert numeric fields
    $numericFields = ['barrel_length', 'overall_length', 'weight', 'capacity', 'purchase_price', 'current_value'];
    foreach ($numericFields as $field) {
        if (!empty($weaponData[$field]) && is_numeric($weaponData[$field])) {
            $weaponData[$field] = (float)$weaponData[$field];
        } else {
            $weaponData[$field] = null;
        }
    }
    
    // Handle date fields
    if (!empty($weaponData['purchase_date'])) {
        try {
            $date = new DateTime($weaponData['purchase_date']);
            $weaponData['purchase_date'] = $date->format('Y-m-d');
        } catch (Exception $e) {
            $weaponData['purchase_date'] = null;
        }
    } else {
        $weaponData['purchase_date'] = null;
    }
    
    return $weaponData;
}

/**
 * Enhanced weapon import processing with comprehensive validation
 */
private function processEnhancedWeaponImport(array $csvData, CsvWeaponValidator $validator, bool $updateExisting, ?int $defaultStoreId): array
{
    $results = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => 0,
        'error_details' => [],
        'duplicate_serials' => []
    ];

    // Prepare weapon data and validate structure
    $weaponDataArray = [];
    $validationResults = [];

    foreach ($csvData as $rowIndex => $row) {
        $lineNumber = $rowIndex + 1;
        
        try {
            // Map CSV row to weapon data
            $weaponData = $this->mapCsvRowToWeaponData($row, $defaultStoreId);
            
            // Clean the data
            $weaponData = $validator->cleanWeaponData($weaponData);
            
            // Validate the weapon data
            $validation = $validator->validateWeapon($weaponData, $lineNumber);
            $validationResults[] = $validation;
            
            if ($validation['valid']) {
                $weaponDataArray[] = $weaponData;
            }
            
        } catch (Exception $e) {
            $results['errors']++;
            $results['error_details'][] = "Line {$lineNumber}: Data mapping error - " . $e->getMessage();
        }
    }

    // Check for duplicate serial numbers within the CSV
    $duplicates = $validator->findDuplicateSerialNumbers($weaponDataArray);
    if (!empty($duplicates)) {
        foreach ($duplicates as $serial => $lines) {
            $results['error_details'][] = "Duplicate serial number '{$serial}' found on lines: " . implode(', ', $lines);
            $results['errors'] += count($lines) - 1; // First occurrence is valid, others are errors
        }
    }

    // Process each valid weapon
    foreach ($weaponDataArray as $index => $weaponData) {
        $lineNumber = $index + 1;
        
        // Skip if this was flagged as a duplicate
        $isDuplicate = false;
        foreach ($duplicates as $serial => $lines) {
            if ($weaponData['serial_number'] === $serial && $lineNumber !== $lines[0]) {
                $isDuplicate = true;
                break;
            }
        }
        
        if ($isDuplicate) {
            $results['skipped']++;
            continue;
        }

        try {
            // Check if weapon exists (by serial number)
            $existingWeapon = $this->weaponRepo->findBySerialNumber($weaponData['serial_number']);
            
            if ($existingWeapon) {
                if ($updateExisting) {
                    $this->weaponRepo->update($weaponData, $existingWeapon['id']);
                    $results['updated']++;
                } else {
                    $results['skipped']++;
                }
            } else {
                $this->weaponRepo->save($weaponData);
                $results['created']++;
            }

        } catch (PDOException $e) {
            $results['errors']++;
            if ($e->getCode() == 23000) {
                $results['error_details'][] = "Line {$lineNumber}: Serial number already exists";
            } else {
                $results['error_details'][] = "Line {$lineNumber}: Database error - " . $e->getMessage();
            }
        } catch (Exception $e) {
            $results['errors']++;
            $results['error_details'][] = "Line {$lineNumber}: " . $e->getMessage();
        }
    }

    // Add validation errors to results
    foreach ($validationResults as $validation) {
        if (!$validation['valid']) {
            $results['errors']++;
            $results['error_details'][] = "Line {$validation['line']}: " . implode(', ', $validation['errors']);
        }
    }

    return $results;
}

/**
 * Set appropriate flash message based on import results
 */
private function setImportResultMessage(array $results): void
{
    $message = sprintf(
        'Import completed! Created: %d, Updated: %d, Skipped: %d',
        $results['created'],
        $results['updated'],
        $results['skipped']
    );

    if ($results['errors'] > 0) {
        $message .= sprintf(', Errors: %d', $results['errors']);
        
        if (!empty($results['error_details'])) {
            $message .= "\n\nError Details:\n";
            
            // Show first 15 errors
            $errorsToShow = array_slice($results['error_details'], 0, 15);
            $message .= implode("\n", $errorsToShow);
            
            if (count($results['error_details']) > 15) {
                $message .= "\n... and " . (count($results['error_details']) - 15) . " more errors.";
            }
        }
        
        $messageType = ($results['created'] + $results['updated']) > 0 ? 'warning' : 'error';
    } else {
        $messageType = 'success';
    }

    setFlashMessage($messageType, $message);
}
   
    public function downloadCsvTemplate(): void
    {
        try {
            // Clean output buffer before headers
            if (ob_get_length()) {
                ob_end_clean();
            }

            $filename = 'weapons_import_template.csv';

            // Set headers for download
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-store, no-cache');
            header('Pragma: no-cache');
            header('Expires: 0');

            // Open output stream
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new Exception('Failed to open output stream.');
            }

            // Add UTF-8 BOM for Excel compatibility
            fwrite($output, "\xEF\xBB\xBF");

            // Template headers
            $headers = [
                'name',
                'type',
                'caliber',
                'serial_number',
                'price',
                'store_id',
                'in_stock',
                'status'
            ];

            fputcsv($output, $headers, ',', '"', '\\'); // added escape char

            // Example data rows
            $examples = [
                ['Glock 19', 'Pistol', '9mm', 'GL19123456', '599.99', '1', '1', 'active'],
                ['AR-15 Rifle', 'Rifle', '.223', 'AR15789012', '899.99', '2', '0', 'discontinued'],
                ['Smith & Wesson 686', 'Revolver', '.357 Magnum', 'SW686345678', '749.99', '1', '1', 'out_of_stock'],
            ];

            foreach ($examples as $example) {
                fputcsv($output, $example, ',', '"', '\\'); // added escape char
            }

            fclose($output);
            exit();

        } catch (Exception $e) {
            if (ob_get_length()) {
                ob_end_clean();
            }

            setFlashMessage('error', 'Error generating template: ' . $e->getMessage());
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


