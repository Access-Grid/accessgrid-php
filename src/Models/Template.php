<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class Template
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $name;
    public ?string $platform;
    public ?string $useCase;
    public ?string $protocol;
    public ?string $createdAt;
    public ?string $lastPublishedAt;
    public ?int $issuedKeysCount;
    public ?int $activeKeysCount;
    public ?array $allowedDeviceCounts;
    public ?array $supportSettings;
    public ?array $termsSettings;
    public ?array $styleSettings;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->platform = $data['platform'] ?? null;
        $this->useCase = $data['use_case'] ?? null;
        $this->protocol = $data['protocol'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->lastPublishedAt = $data['last_published_at'] ?? null;
        $this->issuedKeysCount = $data['issued_keys_count'] ?? null;
        $this->activeKeysCount = $data['active_keys_count'] ?? null;
        $this->allowedDeviceCounts = $data['allowed_device_counts'] ?? null;
        $this->supportSettings = $data['support_settings'] ?? null;
        $this->termsSettings = $data['terms_settings'] ?? null;
        $this->styleSettings = $data['style_settings'] ?? null;
    }
}