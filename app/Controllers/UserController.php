<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;
use App\Models\Store;
use App\Models\Weapon;

class UserController extends Controller
{
    public function index()
    {
        $this->authorize(['super_admin', 'store_user']);
        $users = $this->currentUser->role === 'super_admin'
            ? User::all()
            : User::all(['store_id' => $this->currentUser->store_id]);
        $this->view('users/index', ['users' => $users]);
    }

    public function create()
    {
        $this->authorize(['super_admin']);
        $stores = $this->currentUser->role === 'super_admin'
            ? Store::all()
            : [$this->currentUser->store];
        $this->view('users/create', ['stores' => $stores]);
    }

    public function store()
    {
        $this->authorize(['super_admin']);
        $this->verifyCsrf();

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $storeId = $this->currentUser->role === 'super_admin'
            ? $_POST['store_id'] ?? null
            : $this->currentUser->store_id;

        $errors = $this->validate($_POST, [
            'username' => ['required', 'max:255', 'unique:users,username'],
            'password' => ['required', 'min:8'],
            'role' => ['required', 'in:super_admin,store_user'],
            'store_id' => ['exists:stores,id'], // optional for super_admin
        ]);
        if ($errors) {
            $stores = $this->currentUser->role === 'super_admin'
                ? Store::all()
                : [$this->currentUser->store];
            $this->setError(implode(' ', $errors));
            return $this->view('users/create', ['old' => $_POST, 'stores' => $stores]);
        }

        try {
            $user = new User();
            $user->username = $username;
            $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
            $user->role = $role;
            $user->store_id = $storeId;
            $user->created_at = date('Y-m-d H:i:s');
            $user->updated_at = date('Y-m-d H:i:s');
            $user->save();
        } catch (\Exception $e) {
            $this->setError('Failed to create user: ' . $e->getMessage());
            return $this->view('users/create', ['old' => $_POST]);
        }

        $this->setSuccess('User "' . htmlspecialchars($user->username) . '" created successfully.');
        $this->redirect('/users');
    }

    public function edit($id)
    {
        $this->authorize(['super_admin']);
        $user = User::find($id);
        if (! $user) {
            $this->setError('User not found.');
            return $this->redirect('/users');
        }
        $stores = $this->currentUser->role === 'super_admin'
            ? Store::all()
            : [$this->currentUser->store];
        $this->view('users/edit', ['user' => $user, 'stores' => $stores]);
    }

    public function update($id)
    {
        $this->authorize(['super_admin']);
        $this->verifyCsrf();

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $storeId = $this->currentUser->role === 'super_admin'
            ? $_POST['store_id'] ?? null
            : $this->currentUser->store_id;

        $errors = $this->validate($_POST, [
            'username' => ['required', 'max:255'],
            'role' => ['required', 'in:super_admin,store_user'],
            'store_id' => ['exists:stores,id'], // optional for super_admin
        ]);
        if ($errors) {
            $stores = $this->currentUser->role === 'super_admin'
                ? Store::all()
                : [$this->currentUser->store];
            $this->setError(implode(' ', $errors));
            return $this->view('users/edit', ['user' => $user, 'old' => $_POST, 'stores' => $stores]);
        }

        try {
            $user = User::find($id);
            if (! $user) {
                $this->setError('User not found.');
                return $this->redirect('/users');
            }
            $user->username = $username;
            if (! empty($password)) {
                $user->password_hash = password_hash($password, PASSWORD_DEFAULT);
            }

            $user->role = $role;
            $user->store_id = $storeId;
            $user->updated_at = date('Y-m-d H:i:s');
            $user->save();
        } catch (\Exception $e) {
            $this->setError('Failed to update user: ' . $e->getMessage());
            return $this->view('users/edit', ['user' => $user, 'old' => $_POST]);
        }

        $this->setSuccess('User "' . htmlspecialchars($user->username) . '" updated successfully.');
        $this->redirect('/users');
    }

    public function show($id)
    {
        $this->authorize(['super_admin']); // only super can view arbitrary users
        $user = User::find($id);
        if (! $user) {
            $this->setError('User not found.');
            return $this->redirect('/users');
        }
        // if user is a store_user, list that store’s weapons
        $weapons = [];
        if ($user->role === 'store_user' && $user->store_id) {
            $weapons = Weapon::all(['store_id' => $user->store_id]);
        }
        $this->view('users/show', [
          'user'    => $user,
          'weapons' => $weapons,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(['super_admin', 'store_user']);
        $this->verifyCsrf();
        try {
            $user = User::find($id);
            if (! $user) {
                $this->setError('User not found.');
                return $this->redirect('/users');
            }
            if ($user->id === $this->currentUser->id) {
                $this->setError('Cannot delete your own account');
                $this->redirect('/users');
                return;
            }
            $user->delete($this->currentUser->id); // Soft delete
        } catch (\Exception $e) {
            $this->setError('Failed to delete user: ' . $e->getMessage());
            return $this->redirect('/users');
        }

        $this->setSuccess('User "' . htmlspecialchars($user->username) . '" deleted successfully.');        
        $this->redirect('/users');
    }
}