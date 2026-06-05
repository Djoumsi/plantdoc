<?php
/**
 * Contrôleur de base
 */
abstract class Controller
{
    protected function view(string $template, array $data = [], string $layout = 'main'): void
    {
        extract($data);
        $contentFile = APP_ROOT . "/views/$template.php";
        if (!file_exists($contentFile)) {
            throw new RuntimeException("Vue introuvable : $template");
        }
        ob_start();
        require $contentFile;
        $content = ob_get_clean();
        require APP_ROOT . "/views/layouts/$layout.php";
    }

    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: " . url($url));
        exit;
    }

    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function requireAuth(): void
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'Veuillez vous connecter';
            $this->redirect('/login');
        }
    }

    protected function requireRole(string ...$roles): void
    {
        $this->requireAuth();
        if (!in_array($_SESSION['user_role'] ?? '', $roles, true)) {
            http_response_code(403);
            $this->view('errors/403', [], 'main');
            exit;
        }
    }

    protected function validateCsrf(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!Csrf::verify($token)) {
            http_response_code(419);
            die('Token CSRF invalide');
        }
    }
}
