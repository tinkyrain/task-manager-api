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
        $tags = [];

        $taskId = (int)$request->getAttribute('id') ?? 0; // get task id from request
        $tagsIds = (array)$bodyRequest['tag_id'] ?? []; // get tag id from request

        try {
            $task = R::load('tasks', $taskId); // get task data
            if (!$task->id) $errors[] = 'Task with id ' . $taskId . ' not found'; // task validation

            // get tags data
            foreach ($tagsIds as $tagId) {
                $tag = R::load('tags', $tagId);

                //tag validation
                if ($tag->id) {
                    $tags[] = $tag;
                } else {
                    $errors[] = 'Tag with id ' . $tagId . ' not found';
                }
            }

            // data validation for exists
            if (count($errors) > 0) return $this->createErrorResponse($response, 500, $errors);

            // because our table has name with "_", then we are using with method
            R::ext('xdispense', function( string $type ){
                return R::getRedBean()->dispense( $type );
            });

            //connection tasks and tags
            foreach ($tags as $tag) {
                $task->sharedTags[] = $tag;
            }

            R::store($task);

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