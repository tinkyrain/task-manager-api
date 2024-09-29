<?php

use App\Controllers\TagControllers\TagController;
use App\Controllers\TaskControllers\TaskController;
use App\Controllers\TaskControllers\TaskToTagController;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

return function (App $app) {
    $app->options('/{routes:.*}', function (RequestInterface $request, ResponseInterface $response) {
        return $response;
    });

    //region api version V1
    $app->group('/v1', function () use ($app) {
        //region task routing
        $app->get('/tasks/', [TaskController::class, 'getAll']);
        $app->get('/task/{id}/', [TaskController::class, 'getOne']);
        $app->post('/tasks/', [TaskController::class, 'create']);
        $app->delete('/tasks/{id}/', [TaskController::class, 'delete']);
        $app->put('/tasks/{id}/', [TaskController::class, 'update']);
        //endregion

        //region tags routing
        $app->get('/tags/', [TagController::class, 'getAll']);
        $app->get('/tag/{id}/', [TagController::class, 'getOne']);
        $app->post('/tags/', [TagController::class, 'create']);
        $app->delete('/tags/{id}/', [TagController::class, 'delete']);
        $app->put('/tags/{id}/', [TagController::class, 'update']);
        //endregion

        //region task-to-tags routing
        $app->post('/task/{task_id}/tag/', [TaskToTagController::class, 'add']);
        $app->delete('/task/{task_id}/tag/{tag_id}/', [TaskToTagController::class, 'delete']);
        $app->get('/task/{task_id}/tags/', [TaskToTagController::class, 'getAll']);
        //endregion
    });
    //endregion
};