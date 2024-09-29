<?php

use App\Controllers\TagsControllers\TagsController;
use App\Controllers\TaskControllers\TaskController;
use App\Controllers\TaskControllers\TaskToTagsController;
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
        $app->get('/tags/', [TagsController::class, 'getAll']);
        $app->get('/tag/{id}/', [TagsController::class, 'getOne']);
        $app->post('/tags/', [TagsController::class, 'create']);
        $app->delete('/tags/{id}/', [TagsController::class, 'delete']);
        $app->put('/tags/{id}/', [TagsController::class, 'update']);
        //endregion

        //region task-to-tags routing
        $app->post('/task/{task_id}/tag/', [TaskToTagsController::class, 'add']); //add tag to task
        $app->delete('/task/{task_id}/tag/{tag_id}/', [TaskToTagsController::class, 'delete']); //delete tag in task
        $app->get('/task/{task_id}/tags/', [TaskToTagsController::class, 'getAll']); //delete tag in task
        //endregion
    });
    //endregion
};