<?php

use App\Controllers\TaskController;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

return function (App $app) {
    //region CORS
    $app->options('/{routes:.*}', function (RequestInterface $request, ResponseInterface $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });
    //endregion

    //region task routing
    $app->get('/tasks/', [TaskController::class, 'getAllTasks']);
    $app->post('/tasks/', [TaskController::class, 'createTask']);
    $app->delete('/tasks/{id}', [TaskController::class, 'deleteTask']);
    $app->put('/tasks/{id}', [TaskController::class, 'updateTask']);
    //endregion
};

