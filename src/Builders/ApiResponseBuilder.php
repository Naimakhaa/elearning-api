<?php
// src/Builders/ApiResponseBuilder.php
declare(strict_types=1);

namespace App\Builders;

/**
 * Builder Pattern untuk Response API yang Konsisten
 */
class ApiResponseBuilder
{
    private array $response = [
        'success' => true,
        'status_code' => 200,
        'message' => '',
        'data' => null,
        'errors' => null
    ];

    public function success($data = null, string $message = '', int $statusCode = 200): void
    {
        $this->response['success'] = true;
        $this->response['status_code'] = $statusCode;
        $this->response['message'] = $message;
        $this->response['data'] = $data;
        $this->response['errors'] = null;
        
        $this->send();
    }

    public function error(string $message, int $statusCode = 400, array $errors = []): void
    {
        $this->response['success'] = false;
        $this->response['status_code'] = $statusCode;
        $this->response['message'] = $message;
        $this->response['data'] = null;
        $this->response['errors'] = $errors;
        
        $this->send();
    }

    public function validationError(array $errors): void
    {
        $this->error('Validation failed', 422, $errors);
    }

    public function notFound(string $resource = 'Resource'): void
    {
        $this->error("$resource not found", 404);
    }

    private function send(): void
    {
        http_response_code($this->response['status_code']);
        echo json_encode($this->response);
        exit;
    }
}
?>