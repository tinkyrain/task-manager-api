<?php

namespace App\Repositories\TaskRepositories;

use HttpException;
use RedBeanPHP\R;

class TaskToTagRepository
{
    /**
     * Add a task to tag connection
     *
     * @param int $taskId
     * @param int $tagId
     * @return bool
     * @throws Exception
     */
    public function addTaskToTag(int $taskId, int $tagId): bool
    {
        // Load task and tag
        $task = R::load('tasks', $taskId);
        $tag = R::load('tags', $tagId);

        // Exist check
        if (!$task->id) throw new HttpException('Task not found', 404);
        if (!$tag->id) throw new HttpException('Tag not found', 404);

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
     * @throws Exception
     */
    public function deleteTaskToTag(int $taskId, int $tagId): bool
    {
        // Load task and tag
        $task = R::load('tasks', $taskId);
        $tag = R::load('tags', $tagId);

        // Exist check
        if (!$task->id) throw new HttpException('Task not found', 404);
        if (!$tag->id) throw new HttpException('Tag not found', 404);

        // Delete connection
        $task->sharedTags = array_filter($task->sharedTags, function ($sharedTag) use ($tag) {
            return $sharedTag->id !== $tag->id;
        });

        R::store($task);

        return true;
    }

    /**
     * Get tags for task
     *
     * @param int $taskId
     * @return array
     * @throws Exception
     */
    public function getTagsByTask(int $taskId): array
    {
        // Load task
        $task = R::load('tasks', $taskId);

        // Exist check
        if (!$task->id) throw new HttpException('Task not found', 404);

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