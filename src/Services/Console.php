<?php

namespace AccessGrid\Services;

use AccessGrid\AccessGridClient;
use AccessGrid\Models\Template;
use AccessGrid\Models\PassTemplatePair;
use AccessGrid\Models\LedgerItem;
use AccessGrid\Models\Webhook;

class Console
{
    private AccessGridClient $client;
    public HID $hid;

    public function __construct(AccessGridClient $client)
    {
        $this->client = $client;
        $this->hid = new HID($client);
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
     * List pass template pairs
     */
    public function listPassTemplatePairs(array $params = []): array
    {
        $response = $this->client->get('/v1/console/pass-template-pairs', $params);

        if (isset($response['pass_template_pairs'])) {
            $response['pass_template_pairs'] = array_map(
                fn($pair) => new PassTemplatePair($this->client, $pair),
                $response['pass_template_pairs']
            );
        }

        return $response;
    }

    /**
     * List ledger items
     */
    public function listLedgerItems(array $params = []): array
    {
        $response = $this->client->get('/v1/console/ledger-items', $params);

        if (isset($response['ledger_items'])) {
            $response['ledger_items'] = array_map(
                fn($item) => new LedgerItem($this->client, $item),
                $response['ledger_items']
            );
        }

        return $response;
    }

    /**
     * List webhooks
     */
    public function listWebhooks(array $params = []): array
    {
        $response = $this->client->get('/v1/console/webhooks', $params);

        if (isset($response['webhooks'])) {
            $response['webhooks'] = array_map(
                fn($wh) => new Webhook($this->client, $wh),
                $response['webhooks']
            );
        }

        return $response;
    }

    /**
     * Create a webhook
     */
    public function createWebhook(array $data): Webhook
    {
        $response = $this->client->post('/v1/console/webhooks', $data);
        return new Webhook($this->client, $response);
    }

    /**
     * Delete a webhook
     */
    public function deleteWebhook(string $webhookId): void
    {
        $this->client->delete("/v1/console/webhooks/{$webhookId}");
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