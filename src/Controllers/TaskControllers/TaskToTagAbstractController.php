<?php

namespace App\Controllers\TaskControllers;

use App\Controllers\AbstractController\AbstractController;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RedBeanPHP\R;

class TaskToTagAbstractController extends AbstractController
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
    public function add(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $bodyRequest = json_decode($request->getBody()->getContents(), true); //get request data
            $tags = [];

            $taskId = (int)$request->getAttribute('task_id') ?? 0; // get task id from request
            $tagId = (array)$bodyRequest['tag_id'] ?? 0; // get tag id from request


            $task = R::load('tasks', $taskId); // get task data
            $tag = R::load('tags', $tagId);

            if (!$task->id) return $this->createErrorResponse($response, 404, 'Task not found'); // task validation
            if (!$tag->id) return $this->createErrorResponse($response, 404, 'Tag not found'); // task validation

            //connection tasks and tags
            foreach ($tags as $tag) {
                $task->sharedTags[] = $tag;
            }

            R::store($task);
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, [], 201);

    }

    /**
     * This method delete tags on task
     *
     * METHOD: DELETE
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function delete(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $taskId = (int)$request->getAttribute('task_id'); // Get task id
            $tagId = (int)$request->getAttribute('tag_id'); // Get tag id


            $task = R::load('tasks', $taskId); // load task
            $tag = R::load('tags', $tagId); // load tags

            // Check exist task and tag
            if (!$task->id) {
                return $this->createErrorResponse($response, 404, 'Task not found');
            }

            if (!$tag->id) {
                return $this->createErrorResponse($response, 404, 'Tag not found');
            }

            // Removing the connection
            $task->sharedTags = array_filter($task->sharedTags, function ($sharedTag) use ($tag) {
                return $sharedTag->id !== $tag->id;
            });

            R::store($task); // Save changes
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, [], 200);
    }

    /**
     * This method get tags on task
     *
     * METHOD: GET
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getAll(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $taskId = (int)$request->getAttribute('task_id'); // Get task id
            $task = R::load('tasks', $taskId); //load task
            $result = [];

            //check task exist
            if (!$task->id) return $this->createErrorResponse($response, 404, 'Task not found');

            $tags = $task->sharedTags;

            foreach ($tags as $item) {
                $result['data'][] = [
                    'id' => $item->id,
                    'title' => $item->title,
                ];
            }
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result, 200);
    }
}