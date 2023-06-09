<?php

namespace App\Models;

class Admin extends BaseModel
{
    protected static string $table = 'admins';
    protected static string $primaryKey = 'a_id';

    private int $a_id;
    private ?string $a_first_name;
    private ?string $a_last_name;
    private string $a_user_name;
    private string $a_password;
    private ?int $a_created_at;
    private ?int $a_modified_at;

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
     * @return string|null
     */
    public function getAFirstName(): ?string
    {
        return $this->a_first_name;
    }

    /**
     * @param string|null $a_first_name
     */
    public function setAFirstName(?string $a_first_name): void
    {
        $this->a_first_name = $a_first_name;
    }

    /**
     * @return string|null
     */
    public function getALastName(): ?string
    {
        return $this->a_last_name;
    }

    /**
     * @param string|null $a_last_name
     */
    public function setALastName(?string $a_last_name): void
    {
        $this->a_last_name = $a_last_name;
    }

    /**
     * @return string
     */
    public function getAUserName(): string
    {
        return $this->a_user_name;
    }

    /**
     * @param string $a_user_name
     */
    public function setAUserName(string $a_user_name): void
    {
        $this->a_user_name = $a_user_name;
    }

    /**
     * @return string
     */
    public function getAPassword(): string
    {
        return $this->a_password;
    }

    /**
     * @param string $a_password
     */
    public function setAPassword(string $a_password): void
    {
        $this->a_password = $a_password;
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

    /**
     * @return int|null
     */
    public function getAModifiedAt(): ?int
    {
        return $this->a_modified_at;
    }

    /**
     * @param int|null $a_modified_at
     */
    public function setAModifiedAt(?int $a_modified_at): void
    {
        $this->a_modified_at = $a_modified_at;
    }

}