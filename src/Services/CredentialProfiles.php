<?php

namespace AccessGrid\Services;

use AccessGrid\AccessGridClient;
use AccessGrid\Models\CredentialProfile;

class CredentialProfiles
{
    private AccessGridClient $client;

    public function __construct(AccessGridClient $client)
    {
        $this->client = $client;
    }

    /**
     * List all credential profiles
     *
     * @return CredentialProfile[]
     */
    public function list(): array
    {
        $response = $this->client->get('/v1/console/credential-profiles');

        return array_map(
            fn($item) => new CredentialProfile($this->client, $item),
            $response
        );
    }

    /**
     * Create a credential profile
     */
    public function create(array $data): CredentialProfile
    {
        $response = $this->client->post('/v1/console/credential-profiles', $data);
        return new CredentialProfile($this->client, $response);
    }
}
