<?php

namespace App\Helpers;

class SlugGenerator
{
    public static function generate(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        return $slug;
    }
}
