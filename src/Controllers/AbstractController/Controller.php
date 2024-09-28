<?php

namespace App\Controllers\AbstractController;

use Psr\Http\Message\ResponseInterface;
use RedBeanPHP\OODBBean;

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
        $result = [];

        if (!array_key_exists('data', $data)) {
            $result['data'] = $data;
        } else {
            $result = $data;
        }

        $result['success'] = true;
        $result['status_code'] = $statusCode;

        $response->getBody()->write(json_encode($result));
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
            'status_code' => $statusCode
        ];
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus($statusCode);
    }
}