<?php
namespace App;

class Validator
{
    protected $errors = [];

    // Validates data against specified rules
    public function validate(array $data, array $rules)
    {
        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            if ($rule === 'required' && empty($value)) {
                $this->errors[$field] = "{$field} is required.";
            }
            // Add more validation rules as needed
        }

        return empty($this->errors);
    }

    // Returns validation errors
    public function errors()
    {
        return $this->errors;
    }
}
