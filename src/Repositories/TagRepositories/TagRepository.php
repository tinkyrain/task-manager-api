<?php

namespace App\Repositories\TagRepositories;

use App\Models\TagModels\TagModel;
use Exception;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class TagRepository
{
    /**
     * Get all tags
     *
     * @param int $page
     * @param int $limit
     * @return TagModel[]
     */
    public function getAllTags(int $page = 1, int $limit = 25): array
    {
        $offset = ($page - 1) * $limit;
        $tags = R::find('tags', 'LIMIT ? OFFSET ?', [$limit, $offset]);

        // Convert OODBBean objects to Tag objects
        $tagList = [];
        foreach ($tags as $tag) {
            $tagList[] = new TagModel([
                'id' => $tag->id,
                'title' => $tag->title
            ]);
        }

        return $tagList;
    }

    /**
     * Get a tag by ID
     *
     * @param int $id
     * @return TagModel
     * @throws Exception
     */
    public function getTagById(int $id): TagModel
    {
        $tag = R::load('tags', $id);
        if (!$tag->id) {
            throw new Exception('Tag not found', 400);
        }

        return new TagModel([
            'id' => $tag->id,
            'title' => $tag->title
        ]);
    }

    /**
     * Create a new tag
     *
     * @param array $data
     * @return TagModel
     * @throws SQL
     * @throws Exception
     */
    public function createTag(array $data = []): TagModel
    {
        // Validate tag name
        if (empty($data['title'])) throw new Exception('Tag name is required', 400);

        // Create a new tag
        $tagTable = R::dispense('tags');
        $tagTable->title = $data['title'];
        $newTagId = R::store($tagTable);

        return new TagModel([
            'id' => $newTagId,
            'title' => $data['title']
        ]);
    }

    /**
     * Update a tag
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function updateTag(int $id, array $data): bool
    {
        // Load the tag by ID
        $tag = $this->getTagById($id);

        $tagTable = R::load('tags', $tag->getId());

        // Update tag fields
        foreach ($data as $key => $value) {
            if (isset($tagTable->$key)) {
                $tagTable->$key = $value;
            }
        }

        // Save changes to the database
        R::store($tagTable);

        return true;
    }

    /**
     * Delete a tag
     *
     * @param int $id
     * @return bool
     * @throws Exception
     */
    public function deleteTag(int $id): bool
    {
        // Load the tag by ID
        $tag = $this->getTagById($id);

        $tagTable = R::load('tags', $tag->getId());

        // Delete the tag from the database
        return (bool)R::trash($tagTable);
    }

    /**
     * Return total tag count
     *
     * @return int
     */
    public function getTotalTags(): int
    {
        return R::count('tags');
    }
}