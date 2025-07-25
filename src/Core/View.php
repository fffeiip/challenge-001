<?php

namespace App\Core;

class View
{
    public static function render($viewPath, $data = [])
    {
        extract($data);
        require __DIR__ . '/../Views/' . $viewPath . '.php';
    }

}
