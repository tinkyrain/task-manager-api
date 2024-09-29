<?php

namespace App\Controllers\TaskControllers;

use App\Controllers\AbstractController\AbstractController;
use App\Repositories\TaskRepositories\TaskRepository;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RedBeanPHP\R;

class TaskController extends AbstractController
{
    protected TaskRepository $taskRepository;

    public function __construct()
    {
        $this->taskRepository = new TaskRepository();
    }

    /**
     * This method return all tasks
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
            $page = $request->getQueryParams()['page'] ?? 1;
            $limit = $request->getQueryParams()['limit'] ?? 25;
            $tasks = $this->taskRepository->getAllTasks($page, $limit);

            $result = [
                'data' => $tasks,
                'pagination' => [
                    'total' => $this->taskRepository->getTotalTasks(),
                    'page' => $page,
                    'limit' => $limit,
                ],
            ];
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result);
    }

    /**
     * This method return data for one task
     *
     * METHOD: GET
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getOne(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $taskId = $request->getAttribute('id');
            $task = $this->taskRepository->getTaskById($taskId);
            $result['data'] = $task;
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result);
    }

    /**
     * This method create task
     *
     * METHOD: POST
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function create(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $requestData = json_decode($request->getBody()->getContents(), true);
            $newTaskId = $this->taskRepository->createTask($requestData);
            $result['data'] = $this->taskRepository->getTaskById($newTaskId);
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result, 201);
    }

    /**
     * This method delete task
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
            $id = $request->getAttribute('id');
            $this->taskRepository->deleteTask($id);
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, $e->getCode(), $e->getMessage());
        }

        return $this->createSuccessResponse($response, [], 204);
    }

    /**
     * This method update task
     *
     * METHOD: PUT
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function update(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $taskId = $request->getAttribute('id');
            $requestData = json_decode($request->getBody()->getContents(), true);
            $this->taskRepository->updateTask($taskId, $requestData); // Update task fields
            $result['data'] = $this->taskRepository->getTaskById($taskId);
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, $e->getCode(), $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result, 200);
    }
}