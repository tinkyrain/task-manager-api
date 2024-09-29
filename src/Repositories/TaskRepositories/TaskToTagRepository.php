<?php

namespace App\Repositories\TaskRepositories;

use Exception;
use HttpException;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class TaskToTagRepository
{
    /**
     * Add a task to tag connection
     *
     * @param int $taskId
     * @param int $tagId
     * @return bool
     * @throws SQL
     * @throws Exception
     */
    public function addTaskToTag(int $taskId, int $tagId): bool
    {
        // Load task and tag
        $task = R::load('tasks', $taskId);
        $tag = R::load('tags', $tagId);

        // Exist check
        if (!$task->id) throw new Exception('Task not found', 400);
        if (!$tag->id) throw new Exception('Tag not found', 400);

        $task->sharedTags[] = $tag;

        R::store($task);

        return true;
    }

    /**
     * Delete task and tag connection
     *
     * @param int $taskId
     * @param int $tagId
     * @return bool
     * @throws SQL
     * @throws Exception
     */
    public function deleteTaskToTag(int $taskId, int $tagId): bool
    {
        try {
            R::begin();

            // Load task and tag
            $task = R::load('tasks', $taskId);
            $tag = R::load('tags', $tagId);

            // Exist check
            if (!$task->id) throw new Exception('Task not found', 400);
            if (!$tag->id) throw new Exception('Tag not found', 400);

            // Delete connection
            $task->sharedTags = array_filter($task->sharedTags, function ($sharedTag) use ($tag) {
                return $sharedTag->id !== $tag->id;
            });

            R::store($task);
            R::commit();

            return true;
        } catch (Exception $e) {
            R::rollback();
            throw new Exception($e->getMessage(), $e->getCode() ?? 500);
        }
    }

    /**
     * Get tags for task
     *
     * @param int $taskId
     * @return array
     * @throws HttpException
     */
    public function getTagsByTask(int $taskId): array
    {
        // Load task
        $task = R::load('tasks', $taskId);

        // Exist check
        if (!$task->id) throw new HttpException('Task not found', 400);

        $tags = $task->sharedTags;

        // Prepare data
        $result = [];
        foreach ($tags as $tag) {
            $result[] = [
                'id' => $tag->id,
                'title' => $tag->title,
            ];
        }

        return $result;
    }
}