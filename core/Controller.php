<?php

namespace Core;

use App\Controllers\AuthController;
use App\Models\User;
use Core\Validator;

abstract class Controller
{
    protected $currentUser;

    public function __construct()
    {
        session_start();
        // Ensure CSRF token exists
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        if (empty($_SESSION['user_id']) && ! ($this instanceof AuthController)) {
            $this->redirect('/login');
        }
        if (! empty($_SESSION['user_id'])) {
            $this->currentUser = User::find($_SESSION['user_id']);
        }
    }

    /**
     * Generate hidden field for forms to include CSRF token.
     */
    protected function csrfField(): string
    {
        $token = htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8');
        return "<input type='hidden' name='csrf_token' value='$token'>";
    }

    /**
     * Verify the CSRF token from the request.
     *
     * @throws \Exception if the CSRF token is invalid
     */
    protected function verifyCsrf(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (! hash_equals($_SESSION['csrf_token'], $token)) {
                http_response_code(400);
                echo 'Invalid CSRF token';
                exit;
            }
        }
    }

    /**
     * Authorize access for given user roles.
     *
     * @param array $roles
     */
    protected function authorize(array $roles): void
    {
        if (! in_array($this->currentUser->role, $roles)) {
            $this->setError('You do not have permission to access this page.');
            $this->redirect('/');
        }
    }

    /**
     * Render a view template with data.
     *
     * @param string $template View name (without .php)
     * @param array $data Data for the view
     */
    protected function view(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        // include global header
        include __DIR__ . '/../app/Views/layout/header.php';
        // include the specific view template
        require __DIR__ . '/../app/Views/' . $template . '.php';
        // include global footer
        include __DIR__ . '/../app/Views/layout/footer.php';
    }

    /**
     * Redirect to a URL.
     *
     * @param string $url
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    protected function setSuccess(string $msg): void
    {
        $_SESSION['flash_success'] = $msg;
    }

    protected function setError(string $msg): void
    {
        $_SESSION['flash_error'] = $msg;
    }

    protected function displayFlash(): void
    {
        if (! empty($_SESSION['flash_success'])) {
            echo '<div class="alert success">'.htmlspecialchars($_SESSION['flash_success']).'</div>';
            unset($_SESSION['flash_success']);
        }
        if (! empty($_SESSION['flash_error'])) {
            echo '<div class="alert error">'.htmlspecialchars($_SESSION['flash_error']).'</div>';
            unset($_SESSION['flash_error']);
        }
    }

    /**
     * Validate input against rules.
     * Returns array of errors or empty on success.
     */
    protected function validate(array $data, array $rules): array
    {
        $v = new Validator($data, $rules);
        return $v->passes() ? [] : $v->errors();
    }
}