<?php

namespace App\Models\Customer;

use App\Models\BaseModel;

class Customer extends BaseModel
{
    protected static string $table = 'customers';
    protected static string $primaryKey = 'c_id';
    protected array $fillable = [
        'c_id', 'c_first_name', 'c_last_name', 'c_phone', 'c_password'
    ];

    protected array $guarded = [
        'password'
    ];

    protected array $hidden = [
        'password'
    ];

    /**
     * @return int
     */
    public function getCId(): int
    {
        return $this->c_id;
    }

    /**
     * @param int $c_id
     */
    public function setCId(int $c_id): void
    {
        $this->c_id = $c_id;
    }

    /**
     * @return string
     */
    public function getCFirstName(): string
    {
        return $this->c_first_name;
    }

    /**
     * @param string $c_first_name
     */
    public function setCFirstName(string $c_first_name): void
    {
        $this->c_first_name = $c_first_name;
    }

    /**
     * @return string
     */
    public function getCLastName(): string
    {
        return $this->c_last_name;
    }

    /**
     * @param string $c_last_name
     */
    public function setCLastName(string $c_last_name): void
    {
        $this->c_last_name = $c_last_name;
    }

    /**
     * @return int
     */
    public function getCPhone(): int
    {
        return $this->c_phone;
    }

    /**
     * @param int $c_phone
     */
    public function setCPhone(int $c_phone): void
    {
        $this->c_phone = $c_phone;
    }

    /**
     * @return ?string
     */
    public function getCPassword(): ?string
    {
        return $this->c_password;
    }

    /**
     * @param ?string $c_password
     */
    public function setCPassword(?string $c_password): void
    {
        $this->c_password = $c_password;
    }

    /**
     * @return ?string
     */
    public function getCAvatarImg(): ?string
    {
        return $this->c_avatar_img;
    }

    /**
     * @param ?string $c_avatar_img
     */
    public function setCAvatarImg(?string $c_avatar_img): void
    {
        $this->c_avatar_img = $c_avatar_img;
    }

    /**
     * @return ?string
     */
    public function getCCreatedAt(): ?string
    {
        return $this->c_created_at;
    }

    /**
     * @param ?string $c_created_at
     */
    public function setCCreatedAt(?string $c_created_at): void
    {
        $this->c_created_at = $c_created_at;
    }

    /**
     * @return ?string
     */
    public function getCModifiedAt(): ?string
    {
        return $this->c_modified_at;
    }

    /**
     * @param ?string $c_modified_at
     */
    public function setCModifiedAt(?string $c_modified_at): void
    {
        $this->c_modified_at = $c_modified_at;
    }

    private int $c_id;
    private string $c_first_name;
    private string $c_last_name;
    private int $c_phone;
    private ?string $c_password;
    private ?string $c_avatar_img;
    private ?string $c_created_at;
    private ?string $c_modified_at;

    public function passwords(): array
    {
        return [
            'table' => 'customer_passwords',
            'foreign' => 'cp_customer_id',
            'reference' => 'c_id',
            'fields' => [
                'cp_id','cp_password','cp_used_at','cp_expire_at'
            ],
            'model' => CustomerPassword::class,
            'relation' => 'many'
        ];
    }
}