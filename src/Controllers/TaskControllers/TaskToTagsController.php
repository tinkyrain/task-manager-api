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

        $taskId = (int)$request->getAttribute('task_id') ?? 0; // get task id from request
        $tagsIds = (array)$bodyRequest['tags_id'] ?? []; // get tag id from request

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

    /**
     * This method delete tags on task
     *
     * METHOD: DELETE
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function deleteTagsToTask(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $taskId = (int)$request->getAttribute('task_id'); // Get task id
        $tagId = (int)$request->getAttribute('tag_id'); // Get tag id

        try {
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

            $result = [
                'data' => [],
                'success' => true,
                'errors' => []
            ];

            return $this->createSuccessResponse($response, $result, 200);
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }
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
    public function getTagsToTask(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $taskId = (int)$request->getAttribute('task_id'); // Get task id
        $task = R::load('tasks', $taskId); //load task
        $result = [];

        //check task exist
        if(!$task->id) return $this->createErrorResponse($response, 404, 'Task not found');

        $tags = $task->sharedTags;

        foreach ($tags as $item) {
            $result['data'][] = [
                'id' => $item->id,
                'title' => $item->title,
            ];
        }

        $result['success'] = true;
        $result['errors'] = [];

        return $this->createSuccessResponse($response, $result, 200);
    }
}