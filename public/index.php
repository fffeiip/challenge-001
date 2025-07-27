<?php
// Load the bootstrap file which sets up the environment
require_once __DIR__ . '/../src/bootstrap.php';


$method = $_SERVER['REQUEST_METHOD'];
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
if ($path === '' || $path === 'index.php') {
    $path = 'stores/index';
}
$parts = explode('/', $path);
$controllerName = $parts[0] ?? 'stores';
$actionName = $parts[1] ?? 'index';
$id = $parts[2] ?? null;
$controllerName = $parts[0] ?? 'stores'; // Default controller
$actionName = $parts[1] ?? 'index';     // Default action
$id = $parts[2] ?? null;                 // Optional ID parameter


// Route to the correct controller
switch ($controllerName) {
    case 'stores':
        $controller = new StoreController();
        if ($method === 'GET' && $actionName === 'edit' && $id) {
            $controller->edit($id); // URL: /stores/edit/123
        } elseif ($method === 'POST' && $actionName === 'update' && $id) {
            $controller->update($id); // URL: /stores/update/123 (form submission)
        }elseif($method === 'GET' && $actionName === 'index') {
            $controller->index(); // URL: /stores
        } elseif ($method === 'GET' && $actionName === 'create') {
            $controller->create(); // URL: /stores/create
        } elseif ($method === 'POST' && $actionName === 'store') {
            $controller->store(); // URL: /stores/store (form submission)
        } elseif ($method === 'GET' && $actionName === 'delete' && $id) {
            $controller->delete($id); // URL: /stores/delete/123
        }
         elseif ($method === 'GET' && $actionName === 'show' && $id) {
            $controller->show($id); // URL: /stores/show/123
        }
        // ... other actions like edit, update, delete will go here
        else {
            http_response_code(404);
            render('errors/404');
        }
        break;

     case 'weapons':
        $controller = new WeaponController();
        // The link in layout.php uses /weapons/list, so we treat 'list' as an alias for 'index'
        if ($method === 'GET' && ($actionName === 'index' || $actionName === 'list')) {
            $controller->index(); // URL: /weapons, /weapons/index, /weapons/list
        } elseif ($method === 'GET' && $actionName === 'create') {
            $controller->create(); // URL: /weapons/create
        } elseif ($method === 'POST' && $actionName === 'store') {
            $controller->store(); // URL: /weapons/store (form submission)
        } elseif ($method === 'GET' && $actionName === 'edit' && $id) {
            $controller->edit($id); // URL: /weapons/edit/123
        } elseif ($method === 'POST' && $actionName === 'update' && $id) {
            $controller->update($id); // URL: /weapons/update/123 (form submission)
        } elseif ($method === 'GET' && $actionName === 'delete' && $id) {
            $controller->delete($id); // URL: /weapons/delete/123
        }
         elseif ($method === 'GET' && $actionName === 'pdf' && $id) {
            $controller->pdf($id); // URL: /weapons/pdf/123
        }
        elseif ($method === 'GET' && $actionName === 'store-autocomplete') {
            $controller->storeAutocomplete();
        }
        elseif ($method === 'POST' && $actionName === 'bulk-pdf') {
            $controller->bulkPdf(); // URL: /weapons/bulk-pdf (form submission)
        }
            // NEW CSV ROUTES
        elseif ($method === 'GET' && $actionName === 'export-csv') {
            $controller->exportCsv(); // URL: /weapons/export-csv
        } elseif ($method === 'GET' && $actionName === 'import-csv') {
            $controller->importCsvForm(); // URL: /weapons/import-csv
        } elseif ($method === 'POST' && $actionName === 'import-csv') {
            $controller->importCsv(); // URL: /weapons/import-csv (form submission)
        } elseif ($method === 'GET' && $actionName === 'csv-template') {
            $controller->downloadCsvTemplate(); // URL: /weapons/csv-template
        } 
         else {
            http_response_code(404);
            render('errors/404');
        }
        break;
    default:
        // Basic 404 for now
        http_response_code(404);
        render('errors/404');
        break;
}
