<?php

namespace Core;

/**
 * Wrapper sederhana untuk data HTTP request
 */
class Request
{
    public string $method;
    public string $uri;
    public array $queryParams;
    public array $bodyParams;
    public array $headers;

    public function __construct(
        string $method,
        string $uri,
        array $queryParams = [],
        array $bodyParams = [],
        array $headers = []
    ) {
        $this->method      = strtoupper($method);
        $this->uri         = $uri;
        $this->queryParams = $queryParams;
        $this->bodyParams  = $bodyParams;
        $this->headers     = $headers;
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // ambil body (json/form)
        $body = $_POST;
        $raw  = file_get_contents('php://input');
        if ($raw && empty($body)) {
            $json = json_decode($raw, true);
            if (is_array($json)) {
                $body = $json;
            }
        }

        // headers
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        }

        return new self($method, $uri, $_GET, $body, $headers);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->bodyParams[$key] ?? $this->queryParams[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->queryParams, $this->bodyParams);
    }
}
