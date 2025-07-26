<?php
class StoreController
{
    public function index(): void
    {
        $storeRepo = new StoreRepository();
        
        // Get query parameters
        $params = $_GET; // Contains sort_by, sort_dir, page, per_page, search, city, state_region, country
        
        // Get stores with pagination and filtering
        $result = $storeRepo->findAll($params);
        
        // Get filter options for dropdowns
        $filterOptions = $storeRepo->getFilterOptions();

        render('stores/list', [
            'title' => 'All Stores',
            'stores' => $result['data'],
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
        render('stores/form', [
            'title' => 'Create New Store',
            'store' => [], // Pass empty array for a new store
            'action' => '/stores/store',
            'buttonText' => 'Create Store'
        ]);
    }

    public function edit($id): void
    {
        $repo = new StoreRepository();
        $store = $repo->findById($id);

        if (!$store) {
            http_response_code(404);
            echo "Store not found";
            exit();
        }

        render('stores/form', [
            'title' => 'Edit Store',
            'store' => $store,
            'action' => '/stores/update/' . $id,
            'buttonText' => 'Update Store'
        ]);
    }

    public function update($id): void
    {
        $repo = new StoreRepository();
        $store = $repo->findById($id);

        if (!$store) {
            http_response_code(404);
            echo "Store not found";
            exit();
        }

        $data = $_POST;
        $data['slug'] = generateSlug($data['name']);


        try {
            $repo->update($data,$store,$id);
            setFlashMessage('success', 'Store updated successfully!');
        } catch (PDOException $e) {
            // Check for duplicate entry
            if ($e->getCode() == 23000) {
                setFlashMessage('error', 'A store with that name or slug already exists.');
            } else {
                setFlashMessage('error', 'Error updating store: ' . $e->getMessage());
            }
            // Redirect back to form with old data
            header('Location: /stores/edit/' . $id);
            exit();
        }

        header('Location: /stores');
        exit();
    }

    
    public function delete(int $id): void
    {
        $repo = new StoreRepository();
        $store = $repo->findById($id);

        if (!$store) {
            http_response_code(404);
            setFlashMessage('error', 'Store not found.');
            header('Location: /stores');
            exit();
        }

        try {
            if ($repo->delete($id)) {
                setFlashMessage('success', 'Store deleted successfully!');
            } else {
                setFlashMessage('error', 'Failed to delete the store.');
            }
        } catch (PDOException $e) {
            setFlashMessage('error', 'Error deleting store: ' . $e->getMessage());
        }

        header('Location: /stores');
        exit();
    }


    public function store(): void
    {
        $data = $_POST;

        // Validate the input data
        $validator = new StoreValidator($_POST);
        
        if (!$validator->validate()) {
            // Validation failed - create a combined error message
            $errors = $validator->getErrors();
            $errorMessage = "Please fix the following errors:\n";
            foreach ($errors as $field => $message) {
                $errorMessage .= "• " . ucfirst(str_replace('_', ' ', $field)) . ": " . $message . "\n";
            }
            
            setFlashMessage('error', $errorMessage);
            header('Location: /stores/create');
            exit();
        }

        $data['slug'] = generateSlug($data['name']);
        $repo = new StoreRepository();

        try {
            $repo->save($data);
            setFlashMessage('success', 'Store created successfully!');
        } catch (PDOException $e) {
            // Check for duplicate entry
            if ($e->getCode() == 23000) {
                setFlashMessage('error', 'A store with that name or slug already exists.');
            } else {
                setFlashMessage('error', 'Error creating store: ' . $e->getMessage());
            }
            // Redirect back to form with old data
            header('Location: /stores/create');
            exit();
        }

        header('Location: /stores');
        exit();
    }

   
    public function show(int $id): void
    {
        $storeRepo = new StoreRepository();
        $store = $storeRepo->findById($id);

        if (!$store) {
            http_response_code(404);
            render('errors/404');
            return;
        }

        $weaponRepo = new WeaponRepository();
        
        // Get query parameters for weapon pagination/filtering
        $weaponParams = $_GET; // Contains sort_by, sort_dir, page, per_page, search, type, caliber, etc.
        
        // Get weapons with pagination and filtering
        $weaponResult = $weaponRepo->findByStoreId($id, $weaponParams);
        
        // Get filter options for weapon dropdowns
        $weaponFilterOptions = $weaponRepo->getWeaponFilterOptions($id);

        render('stores/show', [
            'title' => 'Store Details: ' . e($store['name']),
            'store' => $store,
            'weapons' => $weaponResult['data'],
            'weapon_pagination' => [
                'current_page' => $weaponResult['page'],
                'total_pages' => $weaponResult['total_pages'], 
                'per_page' => $weaponResult['per_page'],
                'total' => $weaponResult['total'],
                'has_next' => $weaponResult['has_next'],
                'has_prev' => $weaponResult['has_prev']
            ],
            'weapon_sorting' => [
                'sort_by' => $weaponResult['sort_by'],
                'sort_dir' => $weaponResult['sort_dir']
            ],
            'weapon_filters' => $weaponResult['filters'],
            'weapon_filter_options' => $weaponFilterOptions
        ]);
    }
}

