<?php

namespace Traits;

/**
 * Trait Validatable
 * Menyediakan mekanisme validasi sederhana untuk model.
 */
trait Validatable
{
    private array $errors = [];

    /**
     * Reset semua error validasi.
     */
    public function clearErrors(): void
    {
        $this->errors = [];
    }

    /**
     * Tambahkan error ke daftar.
     */
    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Shortcut untuk validasi field required.
     */
    public function validateRequired(string $field, mixed $value, string $fieldLabel = null): void
    {
        $label = $fieldLabel ?? ucfirst($field);

        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, "{$label} is required");
        }
    }

    /**
     * Apakah ada error validasi?
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Ambil semua error.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
