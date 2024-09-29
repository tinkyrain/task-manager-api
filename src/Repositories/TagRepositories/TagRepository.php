<?php

namespace App\Repositories\TagRepositories;

use Exception;
use RedBeanPHP\OODBBean;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class TagRepository
{
    /**
     * Get all tags
     *
     * @param int $page
     * @param int $limit
     * @return OODBBean[]
     */
    public function getAllTags(int $page = 1, int $limit = 25): array
    {
        $offset = ($page - 1) * $limit;
        return array_values(R::find('tags', 'LIMIT ? OFFSET ?', [$limit, $offset]));
    }

    /**
     * Get a tag by ID
     *
     * @param int $id
     * @return OODBBean
     * @throws Exception
     */
    public function getTagById(int $id): OODBBean
    {
        $tag = R::load('tags', $id);
        if (!$tag->id) throw new Exception('Tag not found', 400);
        return $tag;
    }

    /**
     * Create a new tag
     *
     * @param array $data
     * @return OODBBean|array
     * @throws SQL
     * @throws Exception
     */
    public function createTag(array $data = []): OODBBean|array
    {
        // Validate tag name
        if (empty($data['title'])) throw new Exception('Tag name is required', 400);

        // Create a new tag
        $tagTable = R::dispense('tags');
        $tagTable->title = $data['title'];
        $newTagId = R::store($tagTable);

        return isset($newTagId) ? R::load('tags', $newTagId) : [];
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

        // Update tag fields
        foreach ($data as $key => $value) {
            if (isset($tag->$key)) {
                $tag->$key = $value;
            }
        }

        // Save changes to the database
        R::store($tag);

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

        // Delete the tag from the database
        return (bool)R::trash($tag);
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