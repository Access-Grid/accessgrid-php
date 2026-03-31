<?php

namespace AccessGrid\Services;

use AccessGrid\AccessGridClient;
use AccessGrid\Models\HIDOrg;

class HIDOrgs
{
    private AccessGridClient $client;

    public function __construct(AccessGridClient $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new HID organization
     */
    public function create(array $data): HIDOrg
    {
        $response = $this->client->post('/v1/console/hid/orgs', $data);
        return new HIDOrg($this->client, $response);
    }

    /**
     * List all HID organizations
     */
    public function list(): array
    {
        $response = $this->client->get('/v1/console/hid/orgs');
        return array_map(
            fn($org) => new HIDOrg($this->client, $org),
            $response
        );
    }

    /**
     * Complete HID org registration
     */
    public function activate(array $data): HIDOrg
    {
        $response = $this->client->post('/v1/console/hid/orgs/activate', $data);
        return new HIDOrg($this->client, $response);
    }
}
