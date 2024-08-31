<?php

namespace App\Controllers\TaskControllers;

use App\Controllers\AbstractController\Controller;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RedBeanPHP\R;

class TaskToTagsController extends Controller
{
    /**
     * This method add task to tag connection
     *
     * METHOD: POST
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function addTagsToTask(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $bodyRequest = json_decode($request->getBody()->getContents(), true); //get request data
        $errors = [];

        $taskId = (int)$request->getAttribute('id') ?? 0; // get task id from request
        $tagId = (int)$bodyRequest['tag_id'] ?? 0; // get tag id from request

        try {
            $task = R::load('tasks', $taskId); // get task data
            $tag = R::load('tags', $tagId); // get tag data

            // data validation for exists
            if (!$task->id) $errors[] = 'Task with id ' . $taskId . ' not found';
            if (!$tag->id) $errors[] = 'Tag with id ' . $tagId . ' not found';

            if (count($errors) > 0) return $this->createErrorResponse($response, 500, $errors);

            // because our table has name with "_", then we are using with method
            R::ext('xdispense', function( string $type ){
                return R::getRedBean()->dispense( $type );
            });

            // create task to tag connection
            $taskToTagsTable = R::xdispense('task_tags');
            $taskToTagsTable->task_id = (int)$taskId;
            $taskToTagsTable->tag_id = (int)$tagId;

            // add data in table
            R::store($taskToTagsTable);

            $result = [
                'data' => [],
                'success' => count($errors) === 0,
                'errors' => $errors
            ];

            return $this->createSuccessResponse($response, $result, 201);
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }
    }
}