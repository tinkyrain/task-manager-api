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
        $app->get('/tasks/', [TaskController::class, 'getAllTasks']);
        $app->get('/task/{id}/', [TaskController::class, 'getOneTask']);
        $app->post('/tasks/', [TaskController::class, 'createTask']);
        $app->delete('/tasks/{id}/', [TaskController::class, 'deleteTask']);
        $app->put('/tasks/{id}/', [TaskController::class, 'updateTask']);
        //endregion

        //region tags routing
        $app->get('/tags/', [TagsController::class, 'getAllTags']);
        $app->get('/tag/{id}/', [TagsController::class, 'getOneTag']);
        $app->post('/tags/', [TagsController::class, 'createTag']);
        $app->delete('/tags/{id}/', [TagsController::class, 'deleteTag']);
        $app->put('/tags/{id/}', [TagsController::class, 'updateTag']);
        //endregion

        //region task-to-tags routing
        $app->post('/task/{task_id}/tag/', [TaskToTagsController::class, 'addTagsToTask']); //add tag to task
        $app->delete('/task/{task_id}/tag/{tag_id}/', [TaskToTagsController::class, 'deleteTagsToTask']); //delete tag in task
        $app->get('/task/{task_id}/tags/', [TaskToTagsController::class, 'getTagsToTask']); //delete tag in task
        //endregion
    });
    //endregion
};