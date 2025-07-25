<?php

namespace App\Core;

class View
{
    public static function render($viewPath, $data = [])
    {
        extract($data);
        require __DIR__ . '/../Views/' . $viewPath . '.php';
    }

    public static function renderRaw(string $viewPath, array $data = []): string
    {
        $fullPath = __DIR__ . '/../Views/' . $viewPath . '.php';

        if (!file_exists($fullPath)) {
            throw new \Exception("View not found: $viewPath");
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $fullPath;
        return ob_get_clean();
    }

}
