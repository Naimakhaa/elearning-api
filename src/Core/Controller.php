<?php

namespace Core;

abstract class Controller
{
    protected Request $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request ?? Request::fromGlobals();
    }

    /**
     * Helper kirim response JSON
     */
    protected function json(
        mixed $data,
        int $statusCode = 200,
        ?string $message = null,
        ?array $errors = null
    ): void {
        Response::json([
            'success'     => $statusCode >= 200 && $statusCode < 300,
            'status_code' => $statusCode,
            'message'     => $message,
            'data'        => $data,
            'errors'      => $errors,
        ], $statusCode);
    }
}
