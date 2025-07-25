<?php

namespace App\Helpers;

class SortHelper
{
    public static function sortArrow(string $column, string $currentSort, string $order): string
    {
        if ($column !== $currentSort) {
            return '';
        }
        return $order === 'asc' ? ' ▲' : ' ▼';
    }
}
