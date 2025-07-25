<?php

namespace App\Validators;

class WeaponRequestValidator
{
    protected $data;
    protected $errors = [];

    public function __construct(array $postData)
    {
        $this->data = $postData;
    }

    public function validate(): bool
    {
        $name = trim($this->data['name'] ?? '');
        $type = trim($this->data['type'] ?? '');
        $caliber = trim($this->data['caliber'] ?? '');
        $serial_number = trim($this->data['serial_number'] ?? '');
        $price = trim($this->data['price'] ?? '');
        $in_stock = trim($this->data['in_stock'] ?? '');
        $store_id = trim($this->data['store_id'] ?? '');

        if ($name === '') {
            $this->errors['name'] = 'Name is required.';
        }

        if ($type === '') {
            $this->errors['type'] = 'Type is required.';
        }

        if ($caliber === '') {
            $this->errors['caliber'] = 'Caliber is required.';
        }

        if ($serial_number === '') {
            $this->errors['serial_number'] = 'Serial Number is required.';
        }

        if ($price === '') {
            $this->errors['price'] = 'Price is required.';
        }

        if ($in_stock === '') {
            $this->errors['in_stock'] = 'In Stock is required.';
        }
        if ($store_id === '') {
            $this->errors['store_id'] = 'Store is required.';
        }

        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function old(): array
    {
        return $this->data;
    }
}
