<?php

namespace App\Models\Question;

use App\Models\BaseModel;

class Question extends BaseModel
{
    protected static string $table = 'questions';
    protected static string $primaryKey = 'q_id';

    private int $q_id;
    private string $q_body;
    private string $q_level;
    private ?string $q_desc_answer;
    private string $q_status;
    private string $q_options;
    private ?int $q_created_at;
    private ?int $q_modified_at;

    const DELETED = 'deleted';
    const ENABLED = 'enabled';
    const DISABLED = 'disabled';

    /**
     * @return int
     */
    public function getQId(): int
    {
        return $this->q_id;
    }

    /**
     * @param int $q_id
     */
    public function setQId(int $q_id): void
    {
        $this->q_id = $q_id;
    }

    /**
     * @return string
     */
    public function getQBody(): string
    {
        return $this->q_body;
    }

    /**
     * @param string $q_body
     */
    public function setQBody(string $q_body): void
    {
        $this->q_body = $q_body;
    }

    /**
     * @return string
     */
    public function getQLevel(): string
    {
        return $this->q_level;
    }

    /**
     * @param string $q_level
     */
    public function setQLevel(string $q_level): void
    {
        $this->q_level = $q_level;
    }

    /**
     * @return string|null
     */
    public function getQDescAnswer(): ?string
    {
        return $this->q_desc_answer;
    }

    /**
     * @param string|null $q_desc_answer
     */
    public function setQDescAnswer(?string $q_desc_answer): void
    {
        $this->q_desc_answer = $q_desc_answer;
    }

    /**
     * @return string
     */
    public function getQStatus(): string
    {
        return $this->q_status;
    }

    /**
     * @param string $q_status
     */
    public function setQStatus(string $q_status): void
    {
        $this->q_status = $q_status;
    }

    /**
     * @return array
     */
    public function getQOptions(): array
    {
        return json_decode($this->q_options);
    }

    /**
     * @param string $q_options
     */
    public function setQOptions(string $q_options): void
    {
        $this->q_options = $q_options;
    }

    /**
     * @return int|null
     */
    public function getQCreatedAt(): ?int
    {
        return $this->q_created_at;
    }

    /**
     * @param int|null $q_created_at
     */
    public function setQCreatedAt(?int $q_created_at): void
    {
        $this->q_created_at = $q_created_at;
    }

    /**
     * @return int|null
     */
    public function getQModifiedAt(): ?int
    {
        return $this->q_modified_at;
    }

    /**
     * @param int|null $q_modified_at
     */
    public function setQModifiedAt(?int $q_modified_at): void
    {
        $this->q_modified_at = $q_modified_at;
    }

}