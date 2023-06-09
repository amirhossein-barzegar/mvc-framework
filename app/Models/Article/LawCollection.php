<?php

namespace App\Models\Article;

use App\Models\BaseModel;
use App\Models\Customer\CustomerPassword;

class LawCollection extends BaseModel
{
    protected static string $table = 'law_collections';
    protected static string $primaryKey = 'lc_id';

    private int $lc_id;
    private string $lc_title;

    /**
     * @return int
     */
    public function getLcId(): int
    {
        return $this->lc_id;
    }

    /**
     * @param int $lc_id
     */
    public function setLcId(int $lc_id): void
    {
        $this->lc_id = $lc_id;
    }

    /**
     * @return string
     */
    public function getLcTitle(): string
    {
        return $this->lc_title;
    }

    /**
     * @param string $lc_title
     */
    public function setLcTitle(string $lc_title): void
    {
        $this->lc_title = $lc_title;
    }

    public function sections(): array
    {
        return [
            'table' => 'sections',
            'foreign' => 's_law_id',
            'reference' => 'lc_id',
            'model' => Section::class,
            'relation' => 'many'
        ];
    }
}