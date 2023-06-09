<?php

namespace App\Models\Article;

use App\Models\BaseModel;

class Chapter extends BaseModel
{
    protected static string $table = 'chapters';
    protected static string $primaryKey = 'c_id';

    private int $c_id;
    private string $c_title;
    private int $c_section_id;

    /**
     * @return int
     */
    public function getCId(): int
    {
        return $this->c_id;
    }

    /**
     * @param int $c_id
     */
    public function setCId(int $c_id): void
    {
        $this->c_id = $c_id;
    }

    /**
     * @return string
     */
    public function getCTitle(): string
    {
        return $this->c_title;
    }

    /**
     * @param string $c_title
     */
    public function setCTitle(string $c_title): void
    {
        $this->c_title = $c_title;
    }

    /**
     * @return int
     */
    public function getCSectionId(): int
    {
        return $this->c_section_id;
    }

    /**
     * @param int $c_section_id
     */
    public function setCSectionId(int $c_section_id): void
    {
        $this->c_section_id = $c_section_id;
    }

    public function section(): array
    {
        return [
            'table' => 'sections',
            'foreign' => 'c_section_id',
            'reference' => 's_id',
            'fields' => '*',
            'model' => Section::class,
            'relation' => 'one'
        ];
    }

    public function topics(): array
    {
        return [
            'table' => 'topics',
            'foreign' => 't_chapter_id',
            'reference' => 'c_id',
            'fields' => '*',
            'model' => Topic::class,
            'relation' => 'many'
        ];
    }
}