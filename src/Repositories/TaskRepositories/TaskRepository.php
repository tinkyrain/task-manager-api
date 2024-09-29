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
        if (!$task->id) throw new HttpException('Task not found', 400);
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
        // Validate task data
        if (empty($data['title'])) throw new HttpException('Title is required', 400);
        if (empty($data['creator_id'])) throw new HttpException('Creator id is required', 400);
        if (empty($data['assignee_id'])) throw new HttpException('Assignee id is required', 400);
        if (empty($data['project_id'])) throw new HttpException('Project id is required', 400);

        // Create a new task
        $taskTable = R::dispense('tasks');
        $taskTable->title = $data['title'];
        $taskTable->description = $data['description'] ?? '';
        $taskTable->created_at = R::isoDateTime();
        $taskTable->is_active = $data['is_active'] ?? 'Y';
        $taskTable->assignee_id = $data['assignee_id'];
        $taskTable->creator_id = $data['creator_id'];
        $taskTable->project_id = $data['project_id'];

        return R::store($taskTable);
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

        return true;
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
        // Load the task by ID
        $task = $this->getTaskById($id);

        // Delete the task from the database
        return (bool)R::trash($task);
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