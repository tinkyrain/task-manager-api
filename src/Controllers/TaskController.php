<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class TaskController
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
    public function getAllTasks(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        //get all tasks
        $result = R::findAll('tasks');

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
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
     * @throws SQL
     */
    public function createTask(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $result = [];
        $requestData = $request->getParsedBody(); //get request data
        $errors = []; //error data

        //region validate
        if (empty($requestData['title'])) {
            $errors['title'] = 'Title is required';
        }
        //endregion

        //if validate success then add task
        if (count($errors) === 0) {
            $tasksTable = R::dispense('tasks');
            $tasksTable->title = $requestData['title'];
            $tasksTable->description = $requestData['description'] ?? '';
            $tasksTable->created_at = R::isoDateTime();
            $tasksTable->is_active = $requestData['is_active'] ?? 'Y';
            $tasksTable->assignee_id = $requestData['assignee_id'] ?? 0;
            $tasksTable->creator_id = $requestData['creator_id'] ?? 0;

            $newTaskId = R::store($tasksTable);
        }

        //json result
        $result = [
            'data' => isset($newTaskId) ? R::load('tasks', $newTaskId) : [],
            'success' => count($errors) === 0,
            'errors' => $errors,
        ];

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
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
    public function deleteTask(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $result = [];
        // get task id
        $id = $request->getAttribute('id');

        // load task
        $task = R::load('tasks', (int)$id);

        // check task
        if (!$task->id) {
            $result = [
                'success' => false,
                'errors' => ['Task not found'],
            ];

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        // delete task
        R::trash($task);

        $result = [
            'success' => true,
            'errors' => [],
        ];

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(204);
    }

    public function updateTask(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $result = [];

        $id = $request->getAttribute('id');; // get task id
        $requestData = $request->getParsedBody(); // get request body

        // load task in db
        $task = R::load('tasks', $id);

        // check task
        if (!$task->id) {
            $result = [
                'success' => false,
                'errors' => ['Task not found'],
            ];

            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        //get tasks table column
        $tableColumns = array_keys(R::inspect('tasks'));

        var_dump($requestData);

        // update field
        foreach ($tableColumns as $column) {
            if (isset($requestData[$column])) {
                $task->$column = $requestData[$column];
            }
        }

        // Save changes
        $updateTaskId = R::store($task);

        $result = [
            'data' => R::load('tasks', $updateTaskId),
            'success' => true,
            'errors' => [],
        ];

        // Return data
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}