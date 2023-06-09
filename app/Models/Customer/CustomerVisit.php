<?php

namespace App\Models\Customer;

use App\Models\BaseModel;

class CustomerVisit extends BaseModel
{
    protected static string $table = 'customer_visits';
    protected static string $primaryKey = 'cv_id';

    private int $cs_id;
    private string $cs_agent;
    private string $cs_ip;
    private ?string $cs_customer_id;
    private ?int $cs_logged_in_at;
    private ?int $cs_logged_out_at;

    /**
     * @return int
     */
    public function getCsId(): int
    {
        return $this->cs_id;
    }

    /**
     * @param int $cs_id
     */
    public function setCsId(int $cs_id): void
    {
        $this->cs_id = $cs_id;
    }

    /**
     * @return string
     */
    public function getCsAgent(): string
    {
        return $this->cs_agent;
    }

    /**
     * @param string $cs_agent
     */
    public function setCsAgent(string $cs_agent): void
    {
        $this->cs_agent = $cs_agent;
    }

    /**
     * @return string
     */
    public function getCsIp(): string
    {
        return $this->cs_ip;
    }

    /**
     * @param string $cs_ip
     */
    public function setCsIp(string $cs_ip): void
    {
        $this->cs_ip = $cs_ip;
    }

    /**
     * @return string|null
     */
    public function getCsCustomerId(): ?string
    {
        return $this->cs_customer_id;
    }

    /**
     * @param string|null $cs_customer_id
     */
    public function setCsCustomerId(?string $cs_customer_id): void
    {
        $this->cs_customer_id = $cs_customer_id;
    }

    /**
     * @return int|null
     */
    public function getCsLoggedInAt(): ?int
    {
        return $this->cs_logged_in_at;
    }

    /**
     * @param int|null $cs_logged_in_at
     */
    public function setCsLoggedInAt(?int $cs_logged_in_at): void
    {
        $this->cs_logged_in_at = $cs_logged_in_at;
    }

    /**
     * @return int|null
     */
    public function getCsLoggedOutAt(): ?int
    {
        return $this->cs_logged_out_at;
    }

    /**
     * @param int|null $cs_logged_out_at
     */
    public function setCsLoggedOutAt(?int $cs_logged_out_at): void
    {
        $this->cs_logged_out_at = $cs_logged_out_at;
    }

}