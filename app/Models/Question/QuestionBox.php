<?php

namespace App\Models\Question;

use App\Models\BaseModel;

class QuestionBox extends BaseModel
{
    protected static string $table = 'question_boxes';
    protected static string $primaryKey = 'qb_id';

    private int $qb_id;
    private string $qb_title;
    private ?string $qb_description;
    private string $qb_question_id;

    /**
     * @return int
     */
    public function getQbId(): int
    {
        return $this->qb_id;
    }

    /**
     * @param int $qb_id
     */
    public function setQbId(int $qb_id): void
    {
        $this->qb_id = $qb_id;
    }

    /**
     * @return string
     */
    public function getQbTitle(): string
    {
        return $this->qb_title;
    }

    /**
     * @param string $qb_title
     */
    public function setQbTitle(string $qb_title): void
    {
        $this->qb_title = $qb_title;
    }

    /**
     * @return string|null
     */
    public function getQbDescription(): ?string
    {
        return $this->qb_description;
    }

    /**
     * @param string|null $qb_description
     */
    public function setQbDescription(?string $qb_description): void
    {
        $this->qb_description = $qb_description;
    }

    /**
     * @return string
     */
    public function getQbQuestionId(): string
    {
        return $this->qb_question_id;
    }

    /**
     * @param string $qb_question_id
     */
    public function setQbQuestionId(string $qb_question_id): void
    {
        $this->qb_question_id = $qb_question_id;
    }

}