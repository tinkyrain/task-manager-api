<?php

namespace App\Controllers\TaskControllers;

use App\Controllers\AbstractController\AbstractController;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RedBeanPHP\R;

class TaskAbstractController extends AbstractController
{
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
        $page = empty($request->getQueryParams()['page']) ? 1 : (int)$request->getQueryParams()['page']; // get page
        $limit = empty($request->getQueryParams()['limit']) ? 25 : (int)$request->getQueryParams()['limit']; // get limit data per page
        $offset = ($page - 1) * $limit; // offset

        try {
            $tasks = R::findAll('tasks', 'LIMIT ? OFFSET ?', [$limit, $offset]); // get data
            $totalTasks = R::count('tasks'); // get total task count

            // Формируем результат
            $result = [
                'data' => array_values($tasks),
                'pagination' => [
                    'total' => $totalTasks,
                    'page' => $page,
                    'limit' => $limit,
                ],
            ];
        } catch (Exception|\DivisionByZeroError $e) {
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
            $taskId = (int)$request->getAttribute('id');
            $task = R::load('tasks', $taskId); //load task
            $result = [];

            //check task exist
            if (!$task->id) return $this->createErrorResponse($response, 404, 'Task not found');
            $result['data'] = $task;
        } catch (Exception|\DivisionByZeroError $e) {
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
     * @param $args
     * @return ResponseInterface
     */
    public function create(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        try {
            $requestData = json_decode($request->getBody()->getContents(), true); //get request data
            $errors = []; //error data

            //region validate
            if (empty($requestData['title']))
                return $this->createErrorResponse($response, 400, 'Title is required');

            if (empty($requestData['creator_id']))
                return $this->createErrorResponse($response, 400, 'Creator id is required');

            if (empty($requestData['assignee_id']))
                return $this->createErrorResponse($response, 400, 'Assignee id is required');
            //endregion

            //if validate success then add task

            $tasksTable = R::dispense('tasks');
            $tasksTable->title = $requestData['title'];
            $tasksTable->description = $requestData['description'] ?? '';
            $tasksTable->created_at = R::isoDateTime();
            $tasksTable->is_active = $requestData['is_active'] ?? 'Y';
            $tasksTable->assignee_id = $requestData['assignee_id'];
            $tasksTable->creator_id = $requestData['creator_id'];

            $newTaskId = R::store($tasksTable);

            $result['data'] = isset($newTaskId) ? R::load('tasks', $newTaskId) : [];

        } catch (Exception $e) {
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
     * @param $args
     * @return ResponseInterface
     */
    public function delete(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        try {
            // get task id
            $taskId = (int)$request->getAttribute('id') ?? 0;

            // load task
            $task = R::load('tasks', $taskId);

            // check task
            if (!$task->id) {
                return $this->createErrorResponse($response, 404, 'Task not found');
            }

            // delete task
            R::trash($task);
        } catch (Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, [], 204);
    }

    /**
     * This method update task
     *
     * METHOD: PUT
     *
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function update(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        try {
            $taskId = (int)$request->getAttribute('id');
            $requestData = json_decode($request->getBody()->getContents(), true);

            // Load task from DB
            $task = R::load('tasks', $taskId);

            // Check if task exists
            if (!$task->id) {
                return $this->createErrorResponse($response, 404, 'Task not found');
            }

            // Update task fields from request data
            $tableColumns = array_keys(R::inspect('tasks'));
            foreach ($tableColumns as $column) {
                if (isset($requestData[$column])) {
                    $task->$column = $requestData[$column];
                }
            }

            // Save changes and handle potential errors
            R::store($task);

            $responseData['data'] = $task;
        } catch (Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $responseData, 200);
    }
}