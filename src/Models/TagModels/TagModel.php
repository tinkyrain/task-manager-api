<?php

namespace App\Models\TagModels;

class TagModel
{
    public int $id;
    public string $title;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? 0;
        $this->title = $data['title'] ?? '';
    }

    /**
     * Get tag id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * set tag id
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * get tag name
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * set tag name
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}