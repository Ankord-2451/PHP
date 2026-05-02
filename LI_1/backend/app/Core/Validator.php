<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Reusable server-side form validator.
 */
class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /** @var array<string, mixed> */
    private array $data;

    public function __construct(array $data)
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = is_string($value) ? htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8') : $value;
        }

        $this->data = $sanitized;
    }

    public function required(string $field): static
    {
        if (!isset($this->data[$field]) || $this->data[$field] === '' || $this->data[$field] === null) {
            $this->errors[$field] = 'The ' . $field . ' field is required.';
        }

        return $this;
    }

    public function email(string $field): static
    {
        if (isset($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'The ' . $field . ' must be a valid email address.';
        }

        return $this;
    }

    public function minLength(string $field, int $min): static
    {
        if (isset($this->data[$field]) && is_string($this->data[$field]) && mb_strlen($this->data[$field]) < $min) {
            $this->errors[$field] = 'The ' . $field . ' must be at least ' . $min . ' characters.';
        }

        return $this;
    }

    public function maxLength(string $field, int $max): static
    {
        if (isset($this->data[$field]) && is_string($this->data[$field]) && mb_strlen($this->data[$field]) > $max) {
            $this->errors[$field] = 'The ' . $field . ' may not be greater than ' . $max . ' characters.';
        }

        return $this;
    }

    public function positiveNumber(string $field): static
    {
        if (isset($this->data[$field]) && (!is_numeric($this->data[$field]) || floatval($this->data[$field]) <= 0)) {
            $this->errors[$field] = 'The ' . $field . ' must be a positive number.';
        }

        return $this;
    }

    public function in(string $field, array $options): static
    {
        if (isset($this->data[$field]) && !in_array($this->data[$field], $options, true)) {
            $this->errors[$field] = 'The ' . $field . ' must be one of: ' . implode(', ', $options) . '.';
        }

        return $this;
    }

    public function date(string $field): static
    {
        if (isset($this->data[$field]) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$this->data[$field])) {
            $this->errors[$field] = 'The ' . $field . ' must be a valid date in Y-m-d format.';
        }

        return $this;
    }

    public function dateBefore(string $fieldA, string $fieldB): static
    {
        if (isset($this->data[$fieldA], $this->data[$fieldB]) && $this->data[$fieldA] >= $this->data[$fieldB]) {
            $this->errors[$fieldB] = 'The ' . $fieldB . ' must be after ' . $fieldA . '.';
        }

        return $this;
    }

    public function fails(): bool
    {
        return count($this->errors) > 0;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
