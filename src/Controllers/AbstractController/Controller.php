<?php

namespace App\Controllers\AbstractController;

use Psr\Http\Message\ResponseInterface;

abstract class Controller
{
    /**
     * This method return success response
     *
     * @param ResponseInterface $response
     * @param array $data
     * @param int $statusCode
     * @return ResponseInterface
     */
    public function createSuccessResponse(ResponseInterface $response, array $data, int $statusCode = 200): ResponseInterface
    {
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }

    /**
     * This method return error response
     *
     * @param ResponseInterface $response
     * @param int $statusCode
     * @param string $errorMessage
     * @return ResponseInterface
     */
    public function createErrorResponse(ResponseInterface $response, int $statusCode, string $errorMessage): ResponseInterface
    {
        $result = [
            'success' => false,
            'message' => $errorMessage,
        ];
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}