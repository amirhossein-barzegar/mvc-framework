<?php

namespace App\Models\Customer;

use App\Models\BaseModel;

class CustomerPassword extends BaseModel
{
    protected static string $table = 'customer_passwords';
    protected static string $primaryKey = 'cp_id';
    private int $cp_id;
    private string $cp_password;

    /**
     * @return int
     */
    public function getCpId(): int
    {
        return $this->cp_id;
    }

    /**
     * @param int $cp_id
     */
    public function setCpId(int $cp_id): void
    {
        $this->cp_id = $cp_id;
    }

    /**
     * @return string
     */
    public function getCpPassword(): string
    {
        return $this->cp_password;
    }

    /**
     * @param string $cp_password
     */
    public function setCpPassword(string $cp_password): void
    {
        $this->cp_password = $cp_password;
    }

    /**
     * @return int
     */
    public function getCpCustomerId(): int
    {
        return $this->cp_customer_id;
    }

    /**
     * @param int $cp_customer_id
     */
    public function setCpCustomerId(int $cp_customer_id): void
    {
        $this->cp_customer_id = $cp_customer_id;
    }

    /**
     * @return array|string|null
     */
    public function getCpUsedAt(): array|string|null
    {
        if (!is_null($this->cp_used_at)) {
            return jdate('h:i:s Y-m-d', $this->cp_used_at ?? '');
        }
        return $this->cp_used_at;
    }

    /**
     * @param ?int $cp_used_at
     */
    public function setCpUsedAt(?int $cp_used_at): void
    {
        $this->cp_used_at = $cp_used_at;
    }

    /**
     * @return array|string
     */
    public function getCpCreatedAt(): array|string
    {
        return jdate('h:i:s Y-m-d', $this->cp_created_at ?? '');
    }

    /**
     * @param ?int $cp_created_at
     */
    public function setCpCreatedAt(?int $cp_created_at): void
    {
        $this->cp_created_at = $cp_created_at;
    }
    private int $cp_customer_id;
    private ?int $cp_used_at;

    private ?int $cp_created_at;
    private ?int $cp_expire_at;

    /**
     * @param ?int $cp_expire_at
     */
    public function setCpExpireAt(?int $cp_expire_at): void
    {
        $this->cp_expire_at = $cp_expire_at;
    }

    /**
     * @return array|string
     */
    public function getCpExpireAt(): array|string
    {
        return jdate('h:i:s Y-m-d', $this->cp_expire_at ?? '');
    }

    public function customer() {
        return [
            'table' => 'customers',
            'foreign' => 'cp_customer_id',
            'reference' => 'c_id',
            'fields' => [
                'c_id','c_first_name','c_last_name','c_phone'
            ],
            'model' => Customer::class,
            'relation' => 'one'
        ];
    }
}