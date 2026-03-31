<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\HIDOrg;

class HIDOrgTest extends TestCase
{
    public function testFullData(): void
    {
        $org = new HIDOrg($this->client, [
            'id' => 'org_123',
            'name' => 'Test Org',
            'slug' => 'test-org',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'phone' => '+1-555-1234',
            'full_address' => '123 Main St, City, ST 12345',
            'status' => 'active',
            'created_at' => '2025-06-01T12:00:00Z',
        ]);

        $this->assertEquals('org_123', $org->id);
        $this->assertEquals('Test Org', $org->name);
        $this->assertEquals('test-org', $org->slug);
        $this->assertEquals('Ada', $org->firstName);
        $this->assertEquals('Lovelace', $org->lastName);
        $this->assertEquals('+1-555-1234', $org->phone);
        $this->assertEquals('123 Main St, City, ST 12345', $org->fullAddress);
        $this->assertEquals('active', $org->status);
        $this->assertEquals('2025-06-01T12:00:00Z', $org->createdAt);
    }

    public function testMinimalData(): void
    {
        $org = new HIDOrg($this->client, [
            'id' => 'org_456',
            'name' => 'Minimal Org',
        ]);

        $this->assertEquals('org_456', $org->id);
        $this->assertEquals('Minimal Org', $org->name);
        $this->assertNull($org->slug);
        $this->assertNull($org->firstName);
        $this->assertNull($org->status);
    }

    public function testEmptyData(): void
    {
        $org = new HIDOrg($this->client, []);

        $this->assertNull($org->id);
        $this->assertNull($org->name);
    }
}
