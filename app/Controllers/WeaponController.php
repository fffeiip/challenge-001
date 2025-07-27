<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Weapon;
use App\Models\Store;
use Core\PDFService;
use Core\TableService;

class WeaponController extends Controller
{
    public function index()
    {
        // 1) Fetch paginated, filtered, sorted weapons
        $listing = TableService::paginate(
            'weapons',
            \App\Models\Weapon::class,
            ['id', 'name', 'type', 'caliber', 'serial_number', 'price', 'in_stock', 'status'], // filterable columns
            ['id', 'name', 'type', 'caliber', 'serial_number', 'price', 'in_stock', 'status', 'store'], // sortable columns
            5,    // per-page
            $this->currentUser->role === 'super_admin'
                ? []
                : ['store_id' => $this->currentUser->store_id]
        );

        $weapons = $listing['items'];
        $meta    = $listing['meta'];

        // 2) Eager-load stores to avoid N+1
        $ids = array_unique(array_column($weapons, 'store_id'));
        $storesRaw = Store::whereIn('id', $ids);
        $stores = [];
        foreach ($storesRaw as $s) {
            $stores[$s->id] = $s;
        }

        // 3) Render view with everything
        $this->view('weapons/index', [
            'weapons' => $weapons,
            'stores'  => $stores,
            'meta'    => $meta,
        ]);
    }

    public function create()
    {
        $this->authorize(['super_admin', 'store_user']);
        $stores = $this->currentUser->role === 'super_admin'
            ? Store::all()
            : [Store::find($this->currentUser->store_id)];
        $this->view('weapons/create', ['stores' => $stores]);
    }

    public function store()
    {
        $this->authorize(['super_admin', 'store_user']);
        $this->verifyCsrf();

        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? '');
        $caliber = trim($_POST['caliber'] ?? '');
        $serialNumber = trim($_POST['serial_number'] ?? '');
        $price = trim($_POST['price'] ?? 0.0);
        $inStock = trim($_POST['in_stock'] ?? 0);
        $status = trim($_POST['status'] ?? 'available');
        $storeId = $this->currentUser->role === 'super_admin'
            ? $_POST['store_id']
            : $this->currentUser->store_id;

        // Validate required fields
        $errors = $this->validate($_POST, [
            'name' => ['required', 'max:255'],
            'type' => ['required', 'max:100'],
            'caliber' => ['required', 'max:100'],
            'serial_number' => ['required', 'max:100', 'unique:weapons,serial_number'],
            'price' => ['required', 'numeric', 'min:0'],
            'in_stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:available,out_of_stock,discontinued'],
            'store_id' => ['required', 'exists:stores,id'],
        ]);
        if ($errors) {
            $this->setError(implode(' ', $errors));
            return $this->view('weapons/create', ['old' => $_POST]);
        }

        try {
            $weapon = new Weapon();
            $weapon->store_id = $storeId;
            $weapon->name = $name;
            $weapon->type = $type;
            $weapon->caliber = $caliber;
            $weapon->serial_number = $serialNumber;
            $weapon->price = $price;
            $weapon->in_stock = $inStock;
            $weapon->status = $status;
            $weapon->created_at = date('Y-m-d H:i:s');
            $weapon->updated_at = date('Y-m-d H:i:s');
            $weapon->save();
        } catch (\Exception $e) {
            $this->setError('Failed to create weapon: ' . $e->getMessage());
            return $this->view('weapons/create', ['old' => $_POST]);
        }

        $this->setSuccess('Weapon "' . htmlspecialchars($name) . '" created successfully.');
        $this->redirect('/weapons');
    }

    public function edit($id)
    {
        $weapon = Weapon::findForUser($id, $this->currentUser);
        if (! $weapon) {
            $this->setError('Weapon not found.');
            return $this->redirect('/weapons');
        }
        $stores = $this->currentUser->role === 'super_admin'
            ? Store::all()
            : [Store::find($this->currentUser->store_id)];
        $this->view('weapons/edit', ['weapon' => $weapon, 'stores' => $stores]);
    }

    public function update($id)
    {
        $weapon = Weapon::findForUser($id, $this->currentUser);
        if (! $weapon) {
            $this->setError('Weapon not found.');
            return $this->redirect('/weapons');
        }
        $this->verifyCsrf();
        $errors = $this->validate($_POST, [
            'name' => ['required', 'max:255'],
            'type' => ['required', 'max:100'],
            'caliber' => ['max:100'],
            'serial_number' => ['required', 'max:100'],
            'price' => ['numeric', 'min:0'],
            'in_stock' => ['integer', 'min:0'],
            'status' => ['in:available,out_of_stock,discontinued'],
            'store_id' => ['required', 'exists:stores,id'],
        ]);
        if ($errors) {
            $stores = $this->currentUser->role === 'super_admin'
                ? Store::all()
                : [$this->currentUser->store];
            $this->setError(implode(' ', $errors));
            return $this->view('weapons/edit', ['weapon' => $weapon, 'old' => $_POST, 'stores' => $stores]);
        }

        try {
            $weapon->store_id = $_POST['store_id'];
            $weapon->name = $_POST['name'];
            $weapon->type = $_POST['type'];
            $weapon->caliber = $_POST['caliber'] ?: null;
            $weapon->serial_number = $_POST['serial_number'];
            $weapon->price = $_POST['price'] ?: 0.0;
            $weapon->in_stock = $_POST['in_stock'] ?: 0;
            $weapon->status = $_POST['status'] ?: 'available';
            $weapon->updated_at = date('Y-m-d H:i:s');
            $weapon->save();
        } catch (\Exception $e) {
            // Handle database errors
            $this->setError('Failed to update weapon: ' . $e->getMessage());
            return $this->view('weapons/edit', ['weapon' => $weapon, 'old' => $_POST]);
        }
        
        $this->setSuccess('Weapon "' . htmlspecialchars($weapon->name) . '" updated successfully.');
        $this->redirect('/weapons');
    }

    public function show($id)
    {
        $weapon = Weapon::findForUser($id, $this->currentUser);
        if (! $weapon) {
            $this->setError('Weapon not found.');
            return $this->redirect('/weapons');
        }
        // also get its store
        $store = $weapon->store();
        $this->view('weapons/show', [
          'weapon' => $weapon,
          'store'  => $store,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(['super_admin', 'store_user']);
        $this->verifyCsrf();

        try {
            $weapon = Weapon::findForUser($id, $this->currentUser);
            if (! $weapon) {
                $this->setError('Weapon not found.');
                return $this->redirect('/weapons');
            }
            $weapon->delete($this->currentUser->id);
        } catch (\Exception $e) {
            $this->setError('Failed to delete weapon: ' . $e->getMessage());
            return $this->redirect('/weapons');
        }

        $this->setSuccess('Weapon "' . htmlspecialchars($weapon->name) . '" deleted successfully.');
        $this->redirect('/weapons');
    }

    // Single-weapon detail PDF
    public function pdf($id)
    {
        $weapon = Weapon::findForUser($id, $this->currentUser);
        if (! $weapon) {
            $this->setError('No weapon found for PDF generation.');
            return $this->redirect('/weapons');
        }
        $store = $weapon->store();
        PDFService::detail(
            "weapon_{$weapon->id}.pdf",
            'Weapon Details Report',
            [
                'ID' => $weapon->id,
                'Name' => htmlspecialchars($weapon->name),
                'Type' => htmlspecialchars($weapon->type),
                'Caliber' => htmlspecialchars($weapon->caliber),
                'Serial Number' => htmlspecialchars($weapon->serial_number),
                'Price' => '$' . number_format($weapon->price, 2),
                'In Stock' => $weapon->in_stock,
                'Status' => htmlspecialchars($weapon->status),
                'Store' => isset($store) ? htmlspecialchars($store->name) : '—',
                'Created At' => $weapon->created_at,
            ],
            $this->currentUser,
            'Store Details',
            ['Name', 'Address', 'City', 'State/Region', 'Country', 'Phone', 'Email'],
            array_map(function($s) {
                return [
                    htmlspecialchars($s->name),
                    htmlspecialchars($s->address_line1),
                    htmlspecialchars($s->city),
                    htmlspecialchars($s->state_region),
                    htmlspecialchars($s->country),
                    htmlspecialchars($s->phone),
                    htmlspecialchars($s->email)
                ];
            }, [$store])
        );
    }


    // Bulk PDF of all weapons (scoped)
    public function pdfAll()
    {
        $weapons = Weapon::findByStore($this->currentUser);
        if (count($weapons) === 0) {
            $this->setError('No weapons found for PDF generation.');
            return $this->redirect('/weapons');
        }
        $ids = array_unique(array_column($weapons, 'store_id'));
        $storesRaw = Store::whereIn('id', $ids);
        $stores = [];
        foreach ($storesRaw as $s) {
            $stores[$s->id] = $s->name;
        }

        PDFService::list(
            'weapons_report.pdf',
            'All Weapons Report',
            ['#','Name','Type','Caliber','Serial Number','Price','In Stock','Status','Store'],
            array_map(function($w, $i) use ($stores) {
                return [
                    $i + 1,
                    $w->name,
                    $w->type,
                    $w->caliber,
                    $w->serial_number,
                    '$' . number_format($w->price, 2),
                    $w->in_stock,
                    $w->status,
                    $stores[$w->store_id] ?? '–'
                ];
            }, $weapons, array_keys($weapons)),
            $this->currentUser
        );
    }
}
