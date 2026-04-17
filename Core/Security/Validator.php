<?php

namespace Core\Security;

use Core\Database\DB;

class Validator
{
    private array $data;
    private array $rules;
    private array $errors = [];
    private array $validated = [];
    private array $customMessages = [];

    public function __construct(array $data, array $rules, array $customMessages = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
        $this->validate();
    }

    /**
     * Perform validation
     */
    private function validate(): void
    {
        foreach ($this->rules as $field => $rules) {
            $rulesArray = is_array($rules) ? $rules : explode('|', $rules);

            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }
    }

    /**
     * Apply validation rule
     */
    private function applyRule(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? null;

        // Parse rule and parameters
        [$ruleName, $parameters] = $this->parseRule($rule);

        // Skip other rules if field is not required and empty
        if ($ruleName !== 'required' && !$this->isRequired($field) && empty($value)) {
            return;
        }

        $method = 'validate' . ucfirst($ruleName);

        if (method_exists($this, $method)) {
            $this->$method($field, $value, $parameters);
        }
    }

    /**
     * Parse rule string
     */
    private function parseRule(string $rule): array
    {
        if (strpos($rule, ':') !== false) {
            [$ruleName, $params] = explode(':', $rule, 2);
            $parameters = explode(',', $params);
        } else {
            $ruleName = $rule;
            $parameters = [];
        }

        return [$ruleName, $parameters];
    }

    /**
     * Check if field is required
     */
    private function isRequired(string $field): bool
    {
        $rules = $this->rules[$field] ?? [];
        $rulesArray = is_array($rules) ? $rules : explode('|', $rules);
        return in_array('required', $rulesArray);
    }

    /**
     * Add error message
     */
    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Get custom or default error message
     */
    private function getMessage(string $field, string $rule, array $params = []): string
    {
        $key = "$field.$rule";

        if (isset($this->customMessages[$key])) {
            return $this->customMessages[$key];
        }

        $messages = [
            'required' => "The $field field is required.",
            'email' => "The $field must be a valid email address.",
            'min' => "The $field must be at least " . ($params[0] ?? 'N') . " characters.",
            'max' => "The $field must not exceed " . ($params[0] ?? 'N') . " characters.",
            'numeric' => "The $field must be a number.",
            'integer' => "The $field must be an integer.",
            'string' => "The $field must be a string.",
            'boolean' => "The $field must be true or false.",
            'url' => "The $field must be a valid URL.",
            'in' => "The $field must be one of: " . implode(', ', $params) . ".",
            'confirmed' => "The $field confirmation does not match.",
            'unique' => "The $field has already been taken.",
            'exists' => "The selected $field is invalid.",
            'alpha' => "The $field may only contain letters.",
            'alphanumeric' => "The $field may only contain letters and numbers.",
            'date' => "The $field is not a valid date.",
            'after' => "The $field must be after " . ($params[0] ?? 'specified date') . ".",
            'before' => "The $field must be before " . ($params[0] ?? 'specified date') . ".",
            'same' => "The $field must match " . ($params[0] ?? 'other field') . ".",
            'different' => "The $field must be different from " . ($params[0] ?? 'other field') . ".",
            'regex' => "The $field format is invalid.",
        ];

        return $messages[$rule] ?? "The $field is invalid.";
    }

    // Validation Rules

    private function validateRequired(string $field, $value): void
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, $this->getMessage($field, 'required'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateEmail(string $field, $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $this->getMessage($field, 'email'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function strLength($value): int
    {
        if ($value === null) {
            return 0;
        }
        return strlen((string)$value);
    }

    private function hasRule(string $field, string $rule): bool
    {
        if (!isset($this->rules[$field])) {
            return false;
        }

        $rules = explode('|', $this->rules[$field]);

        foreach ($rules as $r) {
            $ruleName = explode(':', $r)[0];

            if ($ruleName === $rule) {
                return true;
            }
        }

        return false;
    }

    private function isNumericField(string $field): bool
    {
        return
            $this->hasRule($field, 'numeric') ||
            $this->hasRule($field, 'int') ||
            $this->hasRule($field, 'integer') ||
            $this->hasRule($field, 'float');
    }

    private function validateMin(string $field, $value, array $params): void
    {
        $min = (int) $params[0];

        if ($this->isNumericField($field)) {
            if ($value < $min) {
                $this->addError($field, $this->getMessage($field, 'min', $params));
            } else {
                $this->validated[$field] = $value;
            }
        } else {
            if ($this->strLength($value) < $min) {
                $this->addError($field, $this->getMessage($field, 'min', $params));
            } else {
                $this->validated[$field] = $value;
            }
        }
    }

    private function validateMax(string $field, $value, array $params): void
    {
        $max = (int) $params[0];

        if ($this->isNumericField($field)) {
            if ($value > $max) {
                $this->addError($field, $this->getMessage($field, 'max', $params));
            } else {
                $this->validated[$field] = $value;
            }
        } else {
            if ($this->strLength($value) > $max) {
                $this->addError($field, $this->getMessage($field, 'max', $params));
            } else {
                $this->validated[$field] = $value;
            }
        }
    }

    private function validateNumeric(string $field, $value): void
    {
        if (!is_numeric($value)) {
            $this->addError($field, $this->getMessage($field, 'numeric'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateInteger(string $field, $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, $this->getMessage($field, 'integer'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateString(string $field, $value): void
    {
        if (!is_string($value)) {
            $this->addError($field, $this->getMessage($field, 'string'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateBoolean(string $field, $value): void
    {
        if (!in_array($value, [true, false, 0, 1, '0', '1'], true)) {
            $this->addError($field, $this->getMessage($field, 'boolean'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateUrl(string $field, $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, $this->getMessage($field, 'url'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateIn(string $field, $value, array $params): void
    {
        if (!in_array($value, $params)) {
            $this->addError($field, $this->getMessage($field, 'in', $params));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateConfirmed(string $field, $value): void
    {
        $confirmField = $field . '_confirmation';
        $confirmValue = $this->data[$confirmField] ?? null;

        if ($value !== $confirmValue) {
            $this->addError($field, $this->getMessage($field, 'confirmed'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateUnique(string $field, $value, array $params): void
    {
        $table = $params[0];
        $column = $params[1] ?? $field;
        $ignoreId = $params[2] ?? null;

        $query = DB::table($table)->where($column, $value);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->first();

        if ($exists) {
            $this->addError($field, $this->getMessage($field, 'unique'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateExists(string $field, $value, array $params): void
    {
        $table = $params[0];
        $column = $params[1] ?? $field;

        $exists = DB::table($table)->where($column, $value)->first();

        if (!$exists) {
            $this->addError($field, $this->getMessage($field, 'exists'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateAlpha(string $field, $value): void
    {
        if ($value === null || !ctype_alpha((string)$value)) {
            $this->addError($field, $this->getMessage($field, 'alpha'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateAlphanumeric(string $field, $value): void
    {
        if ($value === null || !ctype_alnum((string)$value)) {
            $this->addError($field, $this->getMessage($field, 'alphanumeric'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateDate(string $field, $value): void
    {
        if (strtotime($value) === false) {
            $this->addError($field, $this->getMessage($field, 'date'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateAfter(string $field, $value, array $params): void
    {
        $afterDate = strtotime($params[0]);
        $fieldDate = strtotime($value);

        if ($fieldDate === false || $fieldDate <= $afterDate) {
            $this->addError($field, $this->getMessage($field, 'after', $params));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateBefore(string $field, $value, array $params): void
    {
        $beforeDate = strtotime($params[0]);
        $fieldDate = strtotime($value);

        if ($fieldDate === false || $fieldDate >= $beforeDate) {
            $this->addError($field, $this->getMessage($field, 'before', $params));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateSame(string $field, $value, array $params): void
    {
        $otherField = $params[0];
        $otherValue = $this->data[$otherField] ?? null;

        if ($value !== $otherValue) {
            $this->addError($field, $this->getMessage($field, 'same', $params));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateDifferent(string $field, $value, array $params): void
    {
        $otherField = $params[0];
        $otherValue = $this->data[$otherField] ?? null;

        if ($value === $otherValue) {
            $this->addError($field, $this->getMessage($field, 'different', $params));
        } else {
            $this->validated[$field] = $value;
        }
    }

    private function validateRegex(string $field, $value, array $params): void
    {
        $pattern = $params[0];

        if ($value === null || !preg_match($pattern, $value)) {
            $this->addError($field, $this->getMessage($field, 'regex'));
        } else {
            $this->validated[$field] = $value;
        }
    }

    /**
     * Check if validation fails
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Check if validation passes
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Get all errors
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get validated data
     */
    public function validated(): array
    {
        return $this->validated;
    }

    /**
     * Get first error message
     */
    public function firstError(?string $field = null): ?string
    {
        if ($field) {
            return $this->errors[$field][0] ?? null;
        }

        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? null;
        }

        return null;
    }
}
