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
    public function updateTemplate(array $data): Template
    {
        $templateId = $data['card_template_id'];
        unset($data['card_template_id']);
        $response = $this->client->put("/v1/console/card-templates/{$templateId}", $data);
        return new Template($this->client, $response);
    }

    /**
     * Get details of a card template
     */
    public function readTemplate(array $data): Template
    {
        $templateId = $data['card_template_id'];
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

    /**
     * Get event logs for a card template (alias for getLogs to match API examples)
     */
    public function eventLog(array $data): array
    {
        $templateId = $data['card_template_id'];
        $filters = $data['filters'] ?? [];
        
        $response = $this->client->get("/v1/console/card-templates/{$templateId}/logs", $filters);
        
        // Transform response to match expected format from examples
        $events = $response['events'] ?? $response;
        
        return array_map(function ($item) {
            return (object) $item;
        }, $events);
    }

    /**
     * Get iOS provisioning identifiers for preflight
     */
    public function iosPreflight(array $data): object
    {
        $response = $this->client->post('/v1/console/ios-preflight', $data);
        return (object) $response;
    }
}