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
    public ?array $metadata;

    // Convenience accessors (snake_case aliases)
    public ?string $use_case;
    public ?string $created_at;
    public ?string $last_published_at;
    public ?int $issued_keys_count;
    public ?int $active_keys_count;

    // Convenience accessors extracted from nested objects
    public ?bool $allow_on_multiple_devices;
    public ?int $watch_count;
    public ?int $iphone_count;

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
        $this->metadata = $data['metadata'] ?? null;

        // snake_case aliases
        $this->use_case = $this->useCase;
        $this->created_at = $this->createdAt;
        $this->last_published_at = $this->lastPublishedAt;
        $this->issued_keys_count = $this->issuedKeysCount;
        $this->active_keys_count = $this->activeKeysCount;

        // Convenience accessors from nested objects
        $this->allow_on_multiple_devices = $this->allowedDeviceCounts['allow_on_multiple_devices'] ?? null;
        $this->watch_count = $this->allowedDeviceCounts['watch'] ?? null;
        $this->iphone_count = $this->allowedDeviceCounts['iphone'] ?? null;
    }
}