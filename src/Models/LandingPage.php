<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class LandingPage
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $name;
    public ?string $kind;
    public ?bool $passwordProtected;
    public ?bool $password_protected;
    public ?string $logoUrl;
    public ?string $logo_url;
    public ?string $createdAt;
    public ?string $created_at;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->kind = $data['kind'] ?? null;
        $this->passwordProtected = $data['password_protected'] ?? null;
        $this->password_protected = $data['password_protected'] ?? null;
        $this->logoUrl = $data['logo_url'] ?? null;
        $this->logo_url = $data['logo_url'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
    }
}
