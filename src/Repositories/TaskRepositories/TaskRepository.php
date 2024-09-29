<?php

namespace App\Repositories\TaskRepositories;

use Exception;
use HttpException;
use RedBeanPHP\OODBBean;
use RedBeanPHP\R;

class TaskRepository
{
    /**
     * Get all tasks
     *
     * @param int $page
     * @param int $limit
     * @return OODBBean[]
     */
    public function getAllTasks(int $page = 1, int $limit = 25): array
    {
        $offset = ($page - 1) * $limit;
        $tasks = R::findAll('tasks', 'LIMIT ? OFFSET ?', [$limit, $offset]);
        return array_values($tasks);
    }

    /**
     * Get a task by ID
     *
     * @param int $id
     * @return OODBBean
     * @throws Exception
     */
    public function getTaskById(int $id): OODBBean
    {
        $task = R::load('tasks', $id);
        if (!$task->id) throw new Exception('Task not found', 400);
        return $task;
    }

    /**
     * Create a new task
     *
     * @param array $data
     * @return int
     * @throws Exception
     */
    public function createTask(array $data): int
    {
        try {
            R::begin();

            // Validate task data
            if (empty($data['title'])) throw new Exception('Title is required', 400);
            if (empty($data['creator_id'])) throw new Exception('Creator id is required', 400);
            if (empty($data['assignee_id'])) throw new Exception('Assignee id is required', 400);
            if (empty($data['project_id'])) throw new Exception('Project id is required', 400);

            // Create a new task
            $taskTable = R::dispense('tasks');
            $taskTable->title = $data['title'];
            $taskTable->description = $data['description'] ?? '';
            $taskTable->created_at = R::isoDateTime();
            $taskTable->is_active = $data['is_active'] ?? 'Y';
            $taskTable->assignee_id = $data['assignee_id'];
            $taskTable->creator_id = $data['creator_id'];
            $taskTable->project_id = $data['project_id'];

            $newTask = R::store($taskTable);

            R::commit();

            return $newTask;
        } catch (Exception $e) {
            R::rollback();
            throw new Exception($e->getMessage(), $e->getCode() ?? 500);
        }
    }

    /**
     * Update a task
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function updateTask(int $id, array $data): bool
    {
        try {
            R::begin();

            // Load the task by ID
            $task = $this->getTaskById($id);

            // Update task fields
            foreach ($data as $key => $value) {
                if (isset($task->$key)) {
                    $task->$key = $value;
                }
            }

            // Save changes to the database
            R::store($task);
            R::commit();

            return true;
        } catch (Exception $e) {
            R::rollback();
            throw new Exception($e->getMessage(), $e->getCode() ?? 500);
        }
    }

    /**
     * Delete a task
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function deleteTask(int $id): bool
    {
        try {
            R::begin();

            // Load the task by ID
            $task = $this->getTaskById($id);

            $result = (bool)R::trash($task);

            R::commit();
            // Delete the task from the database
            return $result;
        } catch (Exception $e) {
            R::rollback();
            throw new Exception($e->getMessage(), $e->getCode() ?? 500);
        }
    }

    /**
     * Return total task count
     *
     * @return int
     */
    public function getTotalTasks(): int
    {
        return R::count('tasks');
    }
}