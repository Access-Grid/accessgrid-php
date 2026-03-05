<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class LedgerItemPassTemplate
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $name;
    public ?string $protocol;
    public ?string $platform;
    public ?string $useCase;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['ex_id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->protocol = $data['protocol'] ?? null;
        $this->platform = $data['platform'] ?? null;
        $this->useCase = $data['use_case'] ?? null;
    }
}
