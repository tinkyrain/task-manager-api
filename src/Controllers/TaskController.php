<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TaskController
{
    public function getAllTasks(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = ['message' => 'Hello, Slim API'];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}