<?php

namespace App\Controllers\TaskControllers;

use App\Controllers\AbstractController\AbstractController;
use App\Repositories\TaskRepositories\TaskToTagRepository;
use Exception;
use HttpException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RedBeanPHP\RedException\SQL;

class TaskToTagController extends AbstractController
{
    protected TaskToTagRepository $taskToTagRepository;

    public function __construct()
    {
        $this->taskToTagRepository = new TaskToTagRepository();
    }

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
            $bodyRequest = json_decode($request->getBody()->getContents(), true);

            $tagId = (int)$bodyRequest['tag_id'];
            $taskId = $request->getAttribute('task_id');

            $this->taskToTagRepository->addTaskToTag($taskId, $tagId);
        } catch (Exception|SQL $e) {
            return $this->createErrorResponse($response, $e->getCode() ?? 500, $e->getMessage());
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
            $taskId = $request->getAttribute('task_id');
            $tagId = $request->getAttribute('tag_id');

            $this->taskToTagRepository->deleteTaskToTag($taskId, $tagId);
        } catch (Exception $e) {
            return $this->createErrorResponse($response, $e->getCode() ?? 500, $e->getMessage());
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
            $taskId = $request->getAttribute('task_id');
            $tags = $this->taskToTagRepository->getTagsByTask($taskId);

            $result['data'] = $tags;
        } catch (Exception $e) {
            return $this->createErrorResponse($response, $e->getCode() ?? 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result, 200);
    }
}