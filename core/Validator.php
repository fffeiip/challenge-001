<?php
namespace Core;

use Core\Database;

class Validator
{
    protected $data;
    protected $rules;
    protected $errors = [];

    public function __construct(array $data, array $rules)
    {
        $this->data  = $data;
        $this->rules = $rules;
    }

    public function passes(): bool
    {
        foreach ($this->rules as $field => $rules) {
            $value = trim($this->data[$field] ?? '');
            foreach ((array)$rules as $rule) {
                if ($rule === 'required' && $value === '') {
                    $this->errors[$field] = ucfirst($field) . ' is required.';
                }
                if (strpos($rule, 'max:') === 0) {
                    $len = (int)substr($rule, 4);
                    if (strlen($value) > $len) {
                        $this->errors[$field] = ucfirst($field) . " must be <= {$len} chars.";
                    }
                }
                if ($rule === 'alpha_dash' && ! preg_match('/^[a-z0-9\-]+$/', $value)) {
                    $this->errors[$field] = ucfirst($field) . ' contains invalid characters.';
                }
                if ($rule === 'email' && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->errors[$field] = ucfirst($field) . ' must be a valid email.';
                }
                // custom: unique:table,column
                if (strpos($rule, 'unique:') === 0) {
                    list(, $tableCol) = explode(':', $rule, 2);
                    list($table, $col) = explode(',', $tableCol);
                    $db = Database::getInstance()->pdo();
                    $sql = "SELECT COUNT(*) FROM {$table} WHERE {$col} = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$value]);
                    if ($stmt->fetchColumn() > 0) {
                        $this->errors[$field] = ucfirst($field) . ' must be unique.';
                    }
                }
            }
        }
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
