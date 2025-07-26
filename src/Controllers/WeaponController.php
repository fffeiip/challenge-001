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
}


