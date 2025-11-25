<?php

namespace AccessGrid\Services;

use AccessGrid\AccessGridClient;
use AccessGrid\Models\AccessCard;

class AccessCards
{
    private AccessGridClient $client;

    public function __construct(AccessGridClient $client)
    {
        $this->client = $client;
    }

    /**
     * Issue a new access card
     */
    public function issue(array $data): AccessCard
    {
        $response = $this->client->post('/v1/key-cards', $data);
        return new AccessCard($this->client, $response);
    }

    /**
     * Alias for issue() method to maintain backwards compatibility
     */
    public function provision(array $data): AccessCard
    {
        return $this->issue($data);
    }

    /**
     * Get details about a specific access card
     */
    public function get(string $cardId): AccessCard
    {
        $response = $this->client->get("/v1/key-cards/{$cardId}");
        return new AccessCard($this->client, $response);
    }

    /**
     * Update an existing access card
     */
    public function update(array $data): AccessCard
    {
        $cardId = $data['card_id'];
        unset($data['card_id']);
        $response = $this->client->patch("/v1/key-cards/{$cardId}", $data);
        return new AccessCard($this->client, $response);
    }

    /**
     * List NFC keys provisioned for a particular card template.
     * 
     * @param string $templateId Required. The card template ID to list keys for
     * @param string|null $state Filter keys by state (active, suspended, unlink, deleted)
     * @return AccessCard[] List of AccessCard objects
     */
    public function list(string $templateId, ?string $state = null): array
    {
        $params = ['template_id' => $templateId];
        if ($state !== null) {
            $params['state'] = $state;
        }
        
        $response = $this->client->get('/v1/key-cards', $params);
        $keys = $response['keys'] ?? [];
        
        return array_map(function ($item) {
            return new AccessCard($this->client, $item);
        }, $keys);
    }

    /**
     * Manage card state (suspend/resume/unlink)
     */
    public function manage(string $cardId, string $action): AccessCard
    {
        $response = $this->client->post("/v1/key-cards/{$cardId}/{$action}", []);
        return new AccessCard($this->client, $response);
    }

    /**
     * Suspend an access card
     */
    public function suspend(array $data): AccessCard
    {
        $cardId = $data['card_id'];
        return $this->manage($cardId, 'suspend');
    }

    /**
     * Resume a suspended access card
     */
    public function resume(array $data): AccessCard
    {
        $cardId = $data['card_id'];
        return $this->manage($cardId, 'resume');
    }

    /**
     * Unlink an access card
     */
    public function unlink(array $data): AccessCard
    {
        $cardId = $data['card_id'];
        return $this->manage($cardId, 'unlink');
    }

    /**
     * Delete an access card
     */
    public function delete(array $data): AccessCard
    {
        $cardId = $data['card_id'];
        return $this->manage($cardId, 'delete');
    }
}