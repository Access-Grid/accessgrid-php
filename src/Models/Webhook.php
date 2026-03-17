<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class Webhook
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $name;
    public ?string $url;
    public ?string $authMethod;
    public array $subscribedEvents;
    public ?string $createdAt;
    public ?string $privateKey;
    public ?string $clientCert;
    public ?string $certExpiresAt;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->url = $data['url'] ?? null;
        $this->authMethod = $data['auth_method'] ?? null;
        $this->subscribedEvents = $data['subscribed_events'] ?? [];
        $this->createdAt = $data['created_at'] ?? null;
        $this->privateKey = $data['private_key'] ?? null;
        $this->clientCert = $data['client_cert'] ?? null;
        $this->certExpiresAt = $data['cert_expires_at'] ?? null;
    }
}
