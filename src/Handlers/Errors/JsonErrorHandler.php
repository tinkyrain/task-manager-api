<?php

namespace App\Handlers\Errors;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Psr7\Response;

class JsonErrorHandler
{
    public function __invoke(ServerRequestInterface $request, \Throwable $exception): ResponseInterface
    {
        $response = new Response();
        $data = [
            'success' => false,
            'message' => $exception->getMessage(),
            'status_code' => $exception->getCode(),
        ];

        // Set status code
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
        } elseif ($exception instanceof HttpMethodNotAllowedException) {
            $statusCode = 405; // Method not allowed
        } else {
            $statusCode = 500; // Server error
        }

        $response = $response->withStatus($statusCode);
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json');
    }
}