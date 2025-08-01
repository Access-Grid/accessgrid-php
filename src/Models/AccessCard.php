<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class AccessCard
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $url;
    public ?string $state;
    public ?string $fullName;
    public ?string $expirationDate;
    public ?string $cardNumber;
    public ?string $siteCode;
    public ?string $fileData;
    public ?string $directInstallUrl;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->url = $data['install_url'] ?? null;
        $this->state = $data['state'] ?? null;
        $this->fullName = $data['full_name'] ?? null;
        $this->expirationDate = $data['expiration_date'] ?? null;
        $this->cardNumber = $data['card_number'] ?? null;
        $this->siteCode = $data['site_code'] ?? null;
        $this->fileData = $data['file_data'] ?? null;
        $this->directInstallUrl = $data['direct_install_url'] ?? null;
    }

    public function __toString(): string
    {
        return sprintf("AccessCard(name='%s', id='%s', state='%s')", $this->fullName, $this->id, $this->state);
    }
}