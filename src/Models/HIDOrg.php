<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class HIDOrg
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $name;
    public ?string $slug;
    public ?string $firstName;
    public ?string $lastName;
    public ?string $phone;
    public ?string $fullAddress;
    public ?string $status;
    public ?string $createdAt;

    // snake_case aliases
    public ?string $first_name;
    public ?string $last_name;
    public ?string $full_address;
    public ?string $created_at;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->slug = $data['slug'] ?? null;
        $this->firstName = $data['first_name'] ?? null;
        $this->lastName = $data['last_name'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->fullAddress = $data['full_address'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;

        // snake_case aliases
        $this->first_name = $this->firstName;
        $this->last_name = $this->lastName;
        $this->full_address = $this->fullAddress;
        $this->created_at = $this->createdAt;
    }
}
