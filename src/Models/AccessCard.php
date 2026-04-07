<?php

namespace AccessGrid\Models;

use AccessGrid\AccessGridClient;

class AccessCard
{
    private AccessGridClient $client;
    public ?string $id;
    public ?string $url;
    public ?string $install_url;
    public ?string $state;
    public ?string $fullName;
    public ?string $full_name;
    public ?string $expirationDate;
    public ?string $expiration_date;
    public ?string $cardNumber;
    public ?string $card_number;
    public ?string $siteCode;
    public ?string $site_code;
    public ?string $fileData;
    public ?string $file_data;
    public ?string $directInstallUrl;
    public ?string $direct_install_url;
    public ?string $organizationName;
    public ?string $organization_name;
    public ?string $department;
    public ?string $location;
    public ?string $siteName;
    public ?string $site_name;
    public ?string $workstation;
    public ?string $mailStop;
    public ?string $mail_stop;
    public ?string $companyAddress;
    public ?string $company_address;
    public $details;
    public array $devices;
    public array $metadata;

    public function __construct(AccessGridClient $client, array $data)
    {
        $this->client = $client;
        $this->id = $data['id'] ?? null;
        $this->url = $data['install_url'] ?? null;
        $this->install_url = $data['install_url'] ?? null;
        $this->state = $data['state'] ?? null;
        $this->fullName = $data['full_name'] ?? null;
        $this->full_name = $data['full_name'] ?? null;
        $this->expirationDate = $data['expiration_date'] ?? null;
        $this->expiration_date = $data['expiration_date'] ?? null;
        $this->cardNumber = $data['card_number'] ?? null;
        $this->card_number = $data['card_number'] ?? null;
        $this->siteCode = $data['site_code'] ?? null;
        $this->site_code = $data['site_code'] ?? null;
        $this->fileData = $data['file_data'] ?? null;
        $this->file_data = $data['file_data'] ?? null;
        $this->directInstallUrl = $data['direct_install_url'] ?? null;
        $this->direct_install_url = $data['direct_install_url'] ?? null;
        $this->organizationName = $data['organization_name'] ?? null;
        $this->organization_name = $data['organization_name'] ?? null;
        $this->department = $data['department'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->siteName = $data['site_name'] ?? null;
        $this->site_name = $data['site_name'] ?? null;
        $this->workstation = $data['workstation'] ?? null;
        $this->mailStop = $data['mail_stop'] ?? null;
        $this->mail_stop = $data['mail_stop'] ?? null;
        $this->companyAddress = $data['company_address'] ?? null;
        $this->company_address = $data['company_address'] ?? null;
        $this->details = $data['details'] ?? null;
        $this->devices = $data['devices'] ?? [];
        $this->metadata = $data['metadata'] ?? [];
    }

    public function __toString(): string
    {
        return sprintf("AccessCard(name='%s', id='%s', state='%s')", $this->fullName, $this->id, $this->state);
    }
}