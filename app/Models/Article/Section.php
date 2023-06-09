<?php

namespace App\Models\Article;

use App\Models\BaseModel;

class Section extends BaseModel
{
    protected static string $table = 'sections';
    protected static string $primaryKey = 's_id';

    private int $s_id;
    private string $s_title;
    private int $s_law_id;

    /**
     * @return int
     */
    public function getSId(): int
    {
        return $this->s_id;
    }

    /**
     * @param int $s_id
     */
    public function setSId(int $s_id): void
    {
        $this->s_id = $s_id;
    }

    /**
     * @return string
     */
    public function getSTitle(): string
    {
        return $this->s_title;
    }

    /**
     * @param string $s_title
     */
    public function setSTitle(string $s_title): void
    {
        $this->s_title = $s_title;
    }

    /**
     * @return int
     */
    public function getSLawId(): int
    {
        return $this->s_law_id;
    }

    /**
     * @param int $s_law_id
     */
    public function setSLawId(int $s_law_id): void
    {
        $this->s_law_id = $s_law_id;
    }

    public function lawCollection(): array
    {
        return [
            'table' => 'law_collections',
            'foreign' => 's_law_id',
            'reference' => 'lc_id',
            'fields' => '*',
            'model' => LawCollection::class,
            'relation' => 'one'
        ];
    }

    public function chapters(): array
    {
        return [
            'table' => 'chapters',
            'foreign' => 'c_section_id',
            'reference' => 's_id',
            'fields' => '*',
            'model' => Chapter::class,
            'relation' => 'many'
        ];
    }
}