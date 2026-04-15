<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\AccessCard;

class AccessCardNewFieldsTest extends TestCase
{
    public function testCardWithNewFields(): void
    {
        $data = [
            'id' => 'card_123',
            'state' => 'active',
            'full_name' => 'John Doe',
            'organization_name' => 'Acme Corp',
            'department' => 'Engineering',
            'location' => 'San Francisco',
            'site_name' => 'HQ Building A',
            'workstation' => '4F-207',
            'mail_stop' => 'MS-401',
            'company_address' => '123 Main St, San Francisco, CA 94105',
        ];

        $card = new AccessCard($this->client, $data);

        $this->assertEquals('Acme Corp', $card->organization_name);
        $this->assertEquals('Acme Corp', $card->organizationName);
        $this->assertEquals('Engineering', $card->department);
        $this->assertEquals('San Francisco', $card->location);
        $this->assertEquals('HQ Building A', $card->site_name);
        $this->assertEquals('HQ Building A', $card->siteName);
        $this->assertEquals('4F-207', $card->workstation);
        $this->assertEquals('MS-401', $card->mail_stop);
        $this->assertEquals('MS-401', $card->mailStop);
        $this->assertEquals('123 Main St, San Francisco, CA 94105', $card->company_address);
        $this->assertEquals('123 Main St, San Francisco, CA 94105', $card->companyAddress);
    }

    public function testCardNewFieldsNullWhenAbsent(): void
    {
        $card = new AccessCard($this->client, ['id' => 'card_456']);

        $this->assertNull($card->organization_name);
        $this->assertNull($card->department);
        $this->assertNull($card->location);
        $this->assertNull($card->site_name);
        $this->assertNull($card->workstation);
        $this->assertNull($card->mail_stop);
        $this->assertNull($card->company_address);
    }
}
