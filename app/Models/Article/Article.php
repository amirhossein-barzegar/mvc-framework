<?php

namespace App\Models\Article;

use App\Models\BaseModel;

class Article extends BaseModel
{
    protected static string $table = 'articles';
    protected static string $primaryKey = 'a_id';

    private int $a_id;
    private string $a_name;
    private ?string $a_description;
    private ?string $a_reference_name;
    private ?string $a_tags;
    private int $a_topic_id;
    private ?int $a_created_at;

    /**
     * @return int
     */
    public function getAId(): int
    {
        return $this->a_id;
    }

    /**
     * @param int $a_id
     */
    public function setAId(int $a_id): void
    {
        $this->a_id = $a_id;
    }

    /**
     * @return string
     */
    public function getAName(): string
    {
        return $this->a_name;
    }

    /**
     * @param string $a_name
     */
    public function setAName(string $a_name): void
    {
        $this->a_name = $a_name;
    }

    /**
     * @return string|null
     */
    public function getADescription(): ?string
    {
        return $this->a_description;
    }

    /**
     * @param string|null $a_description
     */
    public function setADescription(?string $a_description): void
    {
        $this->a_description = $a_description;
    }

    /**
     * @return string|null
     */
    public function getAReferenceName(): ?string
    {
        return $this->a_reference_name;
    }

    /**
     * @param string|null $a_reference_name
     */
    public function setAReferenceName(?string $a_reference_name): void
    {
        $this->a_reference_name = $a_reference_name;
    }

    /**
     * @return string|null
     */
    public function getATags(): ?string
    {
        return $this->a_tags;
    }

    /**
     * @param string|null $a_tags
     */
    public function setATags(?string $a_tags): void
    {
        $this->a_tags = $a_tags;
    }

    /**
     * @return int
     */
    public function getATopicId(): int
    {
        return $this->a_topic_id;
    }

    /**
     * @param int $a_topic_id
     */
    public function setATopicId(int $a_topic_id): void
    {
        $this->a_topic_id = $a_topic_id;
    }

    /**
     * @return int|null
     */
    public function getACreatedAt(): ?int
    {
        return $this->a_created_at;
    }

    /**
     * @param int|null $a_created_at
     */
    public function setACreatedAt(?int $a_created_at): void
    {
        $this->a_created_at = $a_created_at;
    }

    public function topic(): array
    {
        return [
            'table' => 'topics',
            'foreign' => 'a_topic_id',
            'reference' => 't_id',
            'fields' => '*',
            'model' => Topic::class,
            'relation' => 'one'
        ];
    }
}