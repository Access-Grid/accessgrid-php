<?php

namespace AccessGrid\Tests\Services;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\HIDOrg;

class HIDOrgsTest extends TestCase
{
    public function testCreateHIDOrg(): void
    {
        $this->expectRequest('POST', '/v1/console/hid/orgs', 201, [
            'id' => 'org_123',
            'name' => 'My Org',
            'slug' => 'my-org',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'phone' => '+1-555-0000',
            'full_address' => '1 Main St, NY NY',
            'status' => 'pending',
            'created_at' => '2025-06-01T12:00:00Z',
        ]);

        $org = $this->client->console->hid->orgs->create([
            'name' => 'My Org',
            'full_address' => '1 Main St, NY NY',
            'phone' => '+1-555-0000',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
        ]);

        $this->assertInstanceOf(HIDOrg::class, $org);
        $this->assertEquals('org_123', $org->id);
        $this->assertEquals('My Org', $org->name);
        $this->assertEquals('my-org', $org->slug);
        $this->assertEquals('Ada', $org->firstName);
        $this->assertEquals('Lovelace', $org->lastName);
        $this->assertEquals('+1-555-0000', $org->phone);
        $this->assertEquals('1 Main St, NY NY', $org->fullAddress);
        $this->assertEquals('pending', $org->status);
    }

    public function testCreateHIDOrgSnakeCaseAliases(): void
    {
        $this->expectRequest('POST', '/v1/console/hid/orgs', 201, [
            'id' => 'org_123',
            'name' => 'My Org',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'full_address' => '1 Main St',
            'created_at' => '2025-06-01T12:00:00Z',
        ]);

        $org = $this->client->console->hid->orgs->create([
            'name' => 'My Org',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'full_address' => '1 Main St',
        ]);

        $this->assertEquals('Ada', $org->first_name);
        $this->assertEquals('Lovelace', $org->last_name);
        $this->assertEquals('1 Main St', $org->full_address);
        $this->assertEquals('2025-06-01T12:00:00Z', $org->created_at);
    }

    public function testListHIDOrgs(): void
    {
        $this->mockResponse(200, [
            [
                'id' => 'org_123',
                'name' => 'Org One',
                'slug' => 'org-one',
                'status' => 'active',
                'created_at' => '2025-01-01T00:00:00Z',
            ],
            [
                'id' => 'org_456',
                'name' => 'Org Two',
                'slug' => 'org-two',
                'status' => 'pending',
                'created_at' => '2025-02-01T00:00:00Z',
            ],
        ]);

        $orgs = $this->client->console->hid->orgs->list();

        $this->assertCount(2, $orgs);
        $this->assertInstanceOf(HIDOrg::class, $orgs[0]);
        $this->assertEquals('org_123', $orgs[0]->id);
        $this->assertEquals('Org One', $orgs[0]->name);
        $this->assertInstanceOf(HIDOrg::class, $orgs[1]);
        $this->assertEquals('org_456', $orgs[1]->id);
    }

    public function testListHIDOrgsEmpty(): void
    {
        $this->mockResponse(200, []);

        $orgs = $this->client->console->hid->orgs->list();

        $this->assertCount(0, $orgs);
    }

    public function testActivateHIDOrg(): void
    {
        $this->expectRequest('POST', '/v1/console/hid/orgs/activate', 200, [
            'id' => 'org_123',
            'name' => 'My Org',
            'slug' => 'my-org',
            'status' => 'active',
            'created_at' => '2025-06-01T12:00:00Z',
        ]);

        $org = $this->client->console->hid->orgs->activate([
            'email' => 'admin@example.com',
            'password' => 'hid-password-123',
        ]);

        $this->assertInstanceOf(HIDOrg::class, $org);
        $this->assertEquals('org_123', $org->id);
        $this->assertEquals('active', $org->status);
    }
}
