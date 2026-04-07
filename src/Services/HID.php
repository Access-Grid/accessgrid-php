<?php

namespace AccessGrid\Services;

use AccessGrid\AccessGridClient;

class HID
{
    private AccessGridClient $client;
    public HIDOrgs $orgs;

    public function __construct(AccessGridClient $client)
    {
        $this->client = $client;
        $this->orgs = new HIDOrgs($client);
    }
}
