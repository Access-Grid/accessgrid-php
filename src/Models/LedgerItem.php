<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class LedgerItem
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $createdAt;
    public $amount;
    public ?string $kind;
    public ?array $metadata;
    public ?LedgerItemAccessPass $accessPass;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->amount = $data['amount'] ?? null;
        $this->kind = $data['kind'] ?? null;
        $this->metadata = $data['metadata'] ?? null;

        $this->accessPass = isset($data['access_pass'])
            ? new LedgerItemAccessPass($client, $data['access_pass'])
            : null;
    }
}
