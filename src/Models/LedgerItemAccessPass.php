<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class LedgerItemAccessPass
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $fullName;
    public ?string $state;
    public ?array $metadata;
    public ?string $unifiedAccessPassExId;
    public ?LedgerItemPassTemplate $passTemplate;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->fullName = $data['full_name'] ?? null;
        $this->state = $data['state'] ?? null;
        $this->metadata = $data['metadata'] ?? null;
        $this->unifiedAccessPassExId = $data['unified_access_pass_ex_id'] ?? null;

        $this->passTemplate = isset($data['pass_template'])
            ? new LedgerItemPassTemplate($client, $data['pass_template'])
            : null;
    }
}
