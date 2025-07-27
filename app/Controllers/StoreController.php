<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Store;
use Core\PDFService;
use Core\TableService;
use Core\Database;
use PDO;
use App\Models\Weapon;

class StoreController extends Controller
{
    public function index()
    {
        // 1) Paginate, sort, and filter stores
        $listing = TableService::paginate(
            'stores',
            Store::class,
            ['id', 'name','slug','address_line1','address_line2','city','state_region','country','phone','email'],   // free-text search columns
            ['id', 'name','slug','city','country','state_region','phone','email'], // sortable columns
            5,  // per-page
            $this->currentUser->role === 'super_admin'
                ? []
                : ['id' => $this->currentUser->store_id] // scope to own store
        );

        $stores = $listing['items'];
        $meta   = $listing['meta'];

        // 2) Eager-load weapon counts to avoid N+1
        $db = Database::getInstance()->pdo();
        $ids = [];
        foreach ($stores as $store) {
            $ids[] = $store->id;
        }
        $totalWeapons = [];
        if (! empty($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT store_id, COUNT(*) AS cnt FROM weapons WHERE store_id IN ({$placeholders}) GROUP BY store_id";
            $stmt = $db->prepare($sql);
            $stmt->execute($ids);
            $counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            // fill missing with 0
            foreach ($ids as $id) {
                $totalWeapons[$id] = $counts[$id] ?? 0;
            }
        }

        // 3) Render view
        $this->view('stores/index', [
            'stores'       => $stores,
            'totalWeapons' => $totalWeapons,
            'meta'         => $meta,
        ]);
    }

    public function create()
    {
        $this->authorize(['super_admin']);
        if ($this->currentUser->role !== 'super_admin') {
            $this->setError('You do not have permission to create stores.');
            return $this->redirect('/stores');
        }
        $this->view('stores/create');
    }

    public function store()
    {
        $this->authorize(['super_admin']);
        $this->verifyCsrf();

        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $addressLine1 = trim($_POST['address_line1'] ?? '');
        $addressLine2 = trim($_POST['address_line2'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $stateRegion = trim($_POST['state_region'] ?? '');
        $country = trim($_POST['country'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Validate required fields
        $errors = $this->validate($_POST, [
            'name' => ['required','max:255','unique:stores,name'],
            'slug' => ['required','alpha_dash','unique:stores,slug'],
            'address_line1' => ['required','max:255'],
            'country' => ['required','max:100'],
            'phone' => ['required','max:20'],
            'email' => ['required','email','max:255'],
        ]);
        if ($errors) {
            $this->setError(implode(' ', $errors));
            return $this->view('stores/create', ['old'=>$_POST]);
        }

        try {
            $store = new Store();
            $store->name = $name;
            $store->slug = $slug;
            $store->address_line1 = $addressLine1;
            $store->address_line2 = $addressLine2;
            $store->city = $city;
            $store->state_region = $stateRegion;
            $store->country = $country;
            $store->phone = $phone;
            $store->email = $email;
            $store->created_at = date('Y-m-d H:i:s');
            $store->updated_at = date('Y-m-d H:i:s');
            $store->save();
        } catch (\PDOException $e) {
            // Handle database errors
            $this->setError('Failed to create store: ' . $e->getMessage());
            return $this->view('stores/create', ['old' => $_POST]);
        }
        

        $this->setSuccess('Store “'.htmlspecialchars($name).'” created successfully.');
        $this->redirect('/stores');
    }

    public function edit($id)
    {
        $store = Store::findForUser($id, $this->currentUser);
        if (! $store) {
            $this->setError('Store not found.');
            return $this->redirect('/stores');
        }
        $this->view('stores/edit', ['store' => $store]);
    }

    public function update($id)
    {
        $store = Store::findForUser($id, $this->currentUser);
        if (! $store) {
            $this->setError('Store not found.');
            return $this->redirect('/stores');
        }
        $this->verifyCsrf();
        $errors = $this->validate($_POST, [
            'name' => ['required','max:255'],
            'slug' => ['required','alpha_dash'],
            'address_line1' => ['required','max:255'],
            'country' => ['required','max:100'],
            'phone' => ['required','max:20'],
            'email' => ['required','email','max:255'],
        ]);
        if ($errors) {
            $this->setError(implode(' ', $errors));
            return $this->view('stores/edit', ['store' => $store, 'old' => $_POST]);
        }

        try {
            $store->name = $_POST['name'];
            $store->slug = $_POST['slug'];
            $store->address_line1 = $_POST['address_line1'];
            $store->address_line2 = $_POST['address_line2'] ?: null;
            $store->city = $_POST['city'];
            $store->state_region = $_POST['state_region'];
            $store->country = $_POST['country'];
            $store->phone = $_POST['phone'];
            $store->email = $_POST['email'];
            $store->updated_at = date('Y-m-d H:i:s');
            $store->save();
        } catch (\PDOException $e) {
            // Handle database errors
            $this->setError('Failed to update store: ' . $e->getMessage());
            return $this->view('stores/edit', ['store' => $store, 'old' => $_POST]);
        }

        $this->setSuccess('Store “'.htmlspecialchars($store->name).'” updated successfully.');
        $this->redirect('/stores');
    }

    public function show($id)
    {
        $store = Store::findForUser($id, $this->currentUser);
        if (! $store) {
            $this->setError('Store not found.');
            return $this->redirect('/stores');
        }

        // Paginate, sort, and filter this store's weapons
        $listing = TableService::paginate(
            'weapons',
            Weapon::class,
            ['name','type','caliber','serial_number'], // filterable columns
            ['id','name','type','caliber','price'],     // sortable columns
            10,                                         // per-page
            ['store_id' => $store->id]                  // scope to this store
        );

        $weapons = $listing['items'];
        $meta    = $listing['meta'];

        // Render view
        $this->view('stores/show', [
            'store'   => $store,
            'weapons' => $weapons,
            'meta'    => $meta,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(['super_admin']);
        $this->verifyCsrf();
        try {
            $store = Store::find($id);
            if (! $store) {
                $this->setError('Store not found.');
                return $this->redirect('/stores');
            }
            // find all weapons in this store and delete them
            $weapons = $store->weapons();
            foreach ($weapons as $weapon) {
                $weapon->delete($this->currentUser->id);
            }
            $store->delete($this->currentUser->id);
        } catch (\PDOException $e) {
            $this->setError('Failed to delete store: ' . $e->getMessage());
            $this->redirect('/stores');
        }

        $this->setSuccess('Store “'.htmlspecialchars($store->name).'” deleted successfully with all its weapons.');       
        $this->redirect('/stores');
    }

    public function pdf($id)
    {
        $store = Store::findForUser($id, $this->currentUser);
        if (! $store) {
            $this->setError('Store not found for PDF generation.');
            return $this->redirect('/stores');
        }
        $weapons = $store->weapons();
        PDFService::detail(
            "store_{$store->id}.pdf",
            "Store Details Report: {$store->name}",
            [
                'Name' => $store->name,
                'Slug' => $store->slug,
                'Address' => $store->address_line1 . ($store->address_line2 ? ' ' . $store->address_line2 : ''),
                'City' => $store->city,
                'State/Region' => $store->state_region,
                'Country' => $store->country,
                'Phone' => $store->phone,
                'Email' => $store->email,
                'Total Weapons' => count($weapons),
            ],
            $this->currentUser,
            'Weapons in this Store',
            ['ID','Name','Type','Caliber','Serial Number','Price', 'In Stock', 'Status'],
            array_map(function($w) {
                return [
                    $w->id,
                    htmlspecialchars($w->name),
                    htmlspecialchars($w->type),
                    htmlspecialchars($w->caliber),
                    htmlspecialchars($w->serial_number),
                    '$'.number_format($w->price,2),
                    htmlspecialchars($w->in_stock),
                    htmlspecialchars($w->status),
                ];
            }, $store->weapons())
        );
    }

    // Bulk PDF of all stores
    public function pdfAll()
    {
        $stores = Store::allForUser($this->currentUser);
        if (count($stores) === 0) {
            $this->setError('No stores found for PDF generation.');
            return $this->redirect('/stores');
        }
        PDFService::list(
            'stores_report.pdf',
            'All Stores Report',
            ['#','Name','Slug','Address','City','State/Region','Country','Phone','Email', 'Total Weapons'],
            array_map(function($s, $id) {
                return [
                    $id + 1,
                    htmlspecialchars($s->name),
                    htmlspecialchars($s->slug),
                    htmlspecialchars($s->address_line1 . ($s->address_line2 ? ' ' . $s->address_line2 : '')),
                    htmlspecialchars($s->city),
                    htmlspecialchars($s->state_region),
                    htmlspecialchars($s->country),
                    htmlspecialchars($s->phone),
                    htmlspecialchars($s->email),
                    count($s->weapons()),
                ];
            }, $stores, array_keys($stores)),
            $this->currentUser
        );
    }
}