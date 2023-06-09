<?php

namespace App\Models\Admin;

use App\Models\BaseModel;

class AdminToken extends BaseModel
{
    protected static string $table = 'admin_tokens';
    protected static string $primaryKey = 'at_id';
    const ENABLED_TOKEN = 'enabled';
    const DISABLED_TOKEN = 'disabled';
    const EXPIRED_TOKEN = 'expired';
    private int $at_id;
    private ?string $at_ip;
    private ?string $at_agent;
    private string $at_status;
    private int $at_admin_id;
    private ?int $at_created_at;
    private ?int $at_expired_at;

    /**
     * @return int
     */
    public function getAtId(): int
    {
        return $this->at_id;
    }

    /**
     * @param int $at_id
     */
    public function setAtId(int $at_id): void
    {
        $this->at_id = $at_id;
    }

    /**
     * @return string|null
     */
    public function getAtIp(): ?string
    {
        return $this->at_ip;
    }

    /**
     * @param string|null $at_ip
     */
    public function setAtIp(?string $at_ip): void
    {
        $this->at_ip = $at_ip;
    }

    /**
     * @return string|null
     */
    public function getAtAgent(): ?string
    {
        return $this->at_agent;
    }

    /**
     * @param string|null $at_agent
     */
    public function setAtAgent(?string $at_agent): void
    {
        $this->at_agent = $at_agent;
    }

    /**
     * @return string
     */
    public function getAtStatus(): string
    {
        return $this->at_status;
    }

    /**
     * @param string $at_status
     */
    public function setAtStatus(string $at_status): void
    {
        $this->at_status = $at_status;
    }

    /**
     * @return int
     */
    public function getAtAdminId(): int
    {
        return $this->at_admin_id;
    }

    /**
     * @param int $at_admin_id
     */
    public function setAtAdminId(int $at_admin_id): void
    {
        $this->at_admin_id = $at_admin_id;
    }

    /**
     * @return int|null
     */
    public function getAtCreatedAt(): ?int
    {
        return $this->at_created_at;
    }

    /**
     * @param int|null $at_created_at
     */
    public function setAtCreatedAt(?int $at_created_at): void
    {
        $this->at_created_at = $at_created_at;
    }

    /**
     * @return int|null
     */
    public function getAtExpiredAt(): ?int
    {
        return $this->at_expired_at;
    }

    /**
     * @param int|null $at_expired_at
     */
    public function setAtExpiredAt(?int $at_expired_at): void
    {
        $this->at_expired_at = $at_expired_at;
    }

}