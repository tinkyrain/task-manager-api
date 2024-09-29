<?php

use App\Controllers\TagControllers\TagController;
use App\Controllers\TaskControllers\TaskAbstractController;
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
        $app->get('/tasks/', [TaskAbstractController::class, 'getAll']);
        $app->get('/task/{id}/', [TaskAbstractController::class, 'getOne']);
        $app->post('/task/', [TaskAbstractController::class, 'create']);
        $app->delete('/task/{id}/', [TaskAbstractController::class, 'delete']);
        $app->put('/task/{id}/', [TaskAbstractController::class, 'update']);
        //endregion

        //region tags routing
        $app->get('/tags/', [TagController::class, 'getAll']);
        $app->get('/tag/{id}/', [TagController::class, 'getOne']);
        $app->post('/tag/', [TagController::class, 'create']);
        $app->delete('/tag/{id}/', [TagController::class, 'delete']);
        $app->put('/tag/{id}/', [TagController::class, 'update']);
        //endregion

        //region task-to-tags routing
        $app->post('/task/{task_id}/tag/', [TaskToTagController::class, 'add']);
        $app->delete('/task/{task_id}/tag/{tag_id}/', [TaskToTagController::class, 'delete']);
        $app->get('/task/{task_id}/tags/', [TaskToTagController::class, 'getAll']);
        //endregion
    });
    //endregion
};