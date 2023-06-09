<?php

namespace App\Models\Question;

use App\Models\BaseModel;

class Category extends BaseModel
{
    protected static string $table = 'categories';
    protected static string $primaryKey = 'ct_id';

    private int $ct_id;
    private string $ct_name;
    private ?string $ct_description;
    private ?int $ct_created_at;
    private ?int $ct_modified_at;

    /**
     * @return int
     */
    public function getCtId(): int
    {
        return $this->ct_id;
    }

    /**
     * @param int $ct_id
     */
    public function setCtId(int $ct_id): void
    {
        $this->ct_id = $ct_id;
    }

    /**
     * @return string
     */
    public function getCtName(): string
    {
        return $this->ct_name;
    }

    /**
     * @param string $ct_name
     */
    public function setCtName(string $ct_name): void
    {
        $this->ct_name = $ct_name;
    }

    /**
     * @return string|null
     */
    public function getCtDescription(): ?string
    {
        return $this->ct_description;
    }

    /**
     * @param string|null $ct_description
     */
    public function setCtDescription(?string $ct_description): void
    {
        $this->ct_description = $ct_description;
    }

    /**
     * @return int|null
     */
    public function getCtCreatedAt(): ?int
    {
        return $this->ct_created_at;
    }

    /**
     * @param int|null $ct_created_at
     */
    public function setCtCreatedAt(?int $ct_created_at): void
    {
        $this->ct_created_at = $ct_created_at;
    }

    /**
     * @return int|null
     */
    public function getCtModifiedAt(): ?int
    {
        return $this->ct_modified_at;
    }

    /**
     * @param int|null $ct_modified_at
     */
    public function setCtModifiedAt(?int $ct_modified_at): void
    {
        $this->ct_modified_at = $ct_modified_at;
    }

}