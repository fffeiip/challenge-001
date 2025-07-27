<?php
namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->verifyCsrf();
            $user = User::all(['username'=>$_POST['username']])[0] ?? null;
            if ($user && password_verify($_POST['password'], $user->password_hash)) {
                $_SESSION['user_id'] = $user->id;
                return $this->redirect('/');
            }
            $this->setError('Invalid credentials');
        }
        $this->view('auth/login');
    }

    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
}