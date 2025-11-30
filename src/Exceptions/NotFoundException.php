<?php
// src/Exceptions/NotFoundException.php
declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception untuk resource tidak ditemukan
 */
class NotFoundException extends \Exception
{
    private string $resourceName;
    private ?int $resourceId;

    public function __construct(string $resourceName = "Resource", ?int $resourceId = null)
    {
        $this->resourceName = $resourceName;
        $this->resourceId = $resourceId;
        
        $message = $resourceId 
            ? "$resourceName with ID $resourceId not found"
            : "$resourceName not found";
            
        parent::__construct($message, 404);
    }

    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    public function getResourceId(): ?int
    {
        return $this->resourceId;
    }

    public function toArray(): array
    {
        return [
            'resource' => $this->resourceName,
            'id' => $this->resourceId,
            'message' => $this->getMessage()
        ];
    }
}
?>