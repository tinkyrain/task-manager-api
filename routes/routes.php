<?php

use App\Controllers\TagsControllers\TagsController;
use App\Controllers\TaskControllers\TaskController;
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

    //region tags routing
    $app->get('/tags/', [TagsController::class, 'getAllTags']);
    $app->post('/tags/', [TagsController::class, 'createTag']);
    $app->delete('/tags/{id}', [TagsController::class, 'deleteTag']);
    $app->put('/tags/{id}', [TagsController::class, 'updateTag']);
    //endregion
};

