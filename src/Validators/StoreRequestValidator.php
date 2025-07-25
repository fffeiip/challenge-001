<?php

namespace App\Validators;

class StoreRequestValidator
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
        $email = trim($this->data['email'] ?? '');
        $phone = trim($this->data['phone'] ?? '');
        $address_line1 = trim($this->data['address_line1'] ?? '');
        $city = trim($this->data['city'] ?? '');
        $state = trim($this->data['state_region'] ?? '');
        $country = trim($this->data['country'] ?? '');

        if ($name === '') {
            $this->errors['name'] = 'Name is required.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Valid email is required.';
        }

        if ($phone === '') {
            $this->errors['phone'] = 'Phone is required.';
        }

        if ($address_line1 === '') {
            $this->errors['address_line1'] = 'Address Line 1 is required.';
        }

        if ($city === '') {
            $this->errors['city'] = 'City is required.';
        }

        if ($state === '') {
            $this->errors['state_region'] = 'State is required.';
        }

        if ($country === '') {
            $this->errors['country'] = 'Country is required.';
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
