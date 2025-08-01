<?php

namespace AccessGrid\Services;

use AccessGrid\AccessGridClient;
use AccessGrid\Models\Template;

class Console
{
    private AccessGridClient $client;

    public function __construct(AccessGridClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new card template
     */
    public function createTemplate(array $data): Template
    {
        $response = $this->client->post('/v1/console/card-templates', $data);
        return new Template($this->client, $response);
    }

    /**
     * Update an existing card template
     */
    public function updateTemplate(string $templateId, array $data): Template
    {
        $response = $this->client->put("/v1/console/card-templates/{$templateId}", $data);
        return new Template($this->client, $response);
    }

    /**
     * Get details of a card template
     */
    public function readTemplate(string $templateId): Template
    {
        $response = $this->client->get("/v1/console/card-templates/{$templateId}");
        return new Template($this->client, $response);
    }

    /**
     * Get event logs for a card template
     */
    public function getLogs(string $templateId, array $params = []): array
    {
        return $this->client->get("/v1/console/card-templates/{$templateId}/logs", $params);
    }
}