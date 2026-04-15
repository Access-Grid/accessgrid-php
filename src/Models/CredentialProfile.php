<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class CredentialProfile
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $aid;
    public ?string $name;
    public ?string $appleId;
    public ?string $apple_id;
    public ?string $createdAt;
    public ?string $created_at;
    public ?string $cardStorage;
    public ?string $card_storage;
    public array $keys;
    public array $files;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->aid = $data['aid'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->appleId = $data['apple_id'] ?? null;
        $this->apple_id = $data['apple_id'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->cardStorage = $data['card_storage'] ?? null;
        $this->card_storage = $data['card_storage'] ?? null;
        $this->keys = $data['keys'] ?? [];
        $this->files = $data['files'] ?? [];
    }
}
