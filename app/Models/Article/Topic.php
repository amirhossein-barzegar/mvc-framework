<?php

namespace App\Models\Article;

use App\Models\BaseModel;

class Topic extends BaseModel
{
    protected static string $table = 'topics';
    protected static string $primaryKey = 't_id';

    private int $t_id;
    private string $t_title;
    private int $t_chapter_id;

    /**
     * @return int
     */
    public function getTId(): int
    {
        return $this->t_id;
    }

    /**
     * @param int $t_id
     */
    public function setTId(int $t_id): void
    {
        $this->t_id = $t_id;
    }

    /**
     * @return string
     */
    public function getTTitle(): string
    {
        return $this->t_title;
    }

    /**
     * @param string $t_title
     */
    public function setTTitle(string $t_title): void
    {
        $this->t_title = $t_title;
    }

    /**
     * @return int
     */
    public function getTChapterId(): int
    {
        return $this->t_chapter_id;
    }

    /**
     * @param int $t_chapter_id
     */
    public function setTChapterId(int $t_chapter_id): void
    {
        $this->t_chapter_id = $t_chapter_id;
    }

    public function chapter(): array
    {
        return [
            'table' => 'chapters',
            'foreign' => 't_chapter_id',
            'reference' => 'c_id',
            'fields' => '*',
            'model' => Chapter::class,
            'relation' => 'one'
        ];
    }

    public function articles(): array
    {
        return [
            'table' => 'articles',
            'foreign' => 'a_topic_id',
            'reference' => 't_id',
            'fields' => '*',
            'model' => Article::class,
            'relation' => 'many'
        ];
    }
}