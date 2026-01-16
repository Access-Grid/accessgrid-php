<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class TemplateInfo
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $name;
    public ?string $platform;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->platform = $data['platform'] ?? null;
    }
}
