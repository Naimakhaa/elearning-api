<?php
// src/Exceptions/ValidationException.php
declare(strict_types=1);

namespace App\Exceptions;

class ValidationException extends \Exception
{
    private array $errors;

    public function __construct(array $errors, string $message = "Validation failed")
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
?>

<?php
// src/Exceptions/NotFoundException.php
declare(strict_types=1);

namespace App\Exceptions;

class NotFoundException extends \Exception
{
    public function __construct(string $resource = "Resource")
    {
        parent::__construct("$resource not found");
    }
}
?>

<?php
// src/Exceptions/EnrollmentException.php
declare(strict_types=1);

namespace App\Exceptions;

class EnrollmentException extends \Exception
{
    public function __construct(string $message = "Enrollment failed")
    {
        parent::__construct($message);
    }
}
?>