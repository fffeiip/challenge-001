<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\Store;
use App\Models\Weapon;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $storesCount  = ($this->currentUser->role === 'super_admin') ? count(Store::all()) : count(Store::all(['id' => $this->currentUser->store_id]));
        $weaponsCount = ($this->currentUser->role === 'super_admin') ? count(Weapon::all()) : count(Weapon::all(['store_id' => $this->currentUser->store_id]));
        $usersCount   = ($this->currentUser->role === 'super_admin') ? count(User::all()) : count(User::all(['store_id' => $this->currentUser->store_id]));

        return $this->view('dashboard/index', ['storesCount' => $storesCount, 'weaponsCount' => $weaponsCount, 'usersCount' => $usersCount, 'userRole' => $this->currentUser->role]);
    }
}