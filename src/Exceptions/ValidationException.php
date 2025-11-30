<?php
// src/Exceptions/ValidationException.php
declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception untuk validasi data gagal
 */
class ValidationException extends \Exception
{
    private array $errors;

    public function __construct(array $errors, string $message = "Validation failed")
    {
        $this->errors = $errors;
        parent::__construct($message, 422);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        return !empty($this->errors) ? reset($this->errors) : null;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'errors' => $this->errors
        ];
    }
}
?>