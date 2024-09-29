<?php

namespace App\Repositories\TagRepositories;

use Exception;
use HttpException;
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
        return R::find('tags', 'LIMIT ? OFFSET ?', [$limit, $offset]);
    }

    /**
     * Get a tag by ID
     *
     * @param int $id
     * @return OODBBean|null
     * @throws HttpException
     */
    public function getTagById(int $id): ?OODBBean
    {
        $tag = R::load('tags', $id);
        if (!$tag->id) throw new HttpException('Tag not found', 400);
        return $tag;
    }

    /**
     * Create a new tag
     *
     * @param string $name
     * @return int
     * @throws SQL
     * @throws Exception
     */
    public function createTag(string $name): int
    {
        // Validate tag name
        if (empty($name)) throw new HttpException('Tag name is required', 400);

        // Create a new tag
        $tagTable = R::dispense('tags');
        $tagTable->name = $name;
        $newTagId = R::store($tagTable);

        return (int)$newTagId;
    }

    /**
     * Update a tag
     *
     * @param int $id
     * @param array $data
     * @return bool
     * @throws SQL
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
        return (bool)R::store($tag);
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
}
