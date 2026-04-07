<?php

namespace AccessGrid\Tests\Services;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\CredentialProfile;

class CredentialProfilesTest extends TestCase
{
    public function testListCredentialProfiles(): void
    {
        $this->expectRequest('GET', '/v1/console/credential-profiles', 200, [
            [
                'id' => 'cp_123',
                'aid' => 'F0394148',
                'name' => 'Main Office Profile',
                'apple_id' => 'apple_456',
                'created_at' => '2025-06-01T12:00:00Z',
                'card_storage' => 'desfire',
                'keys' => [],
                'files' => [],
            ],
        ]);

        $profiles = $this->client->console->credentialProfiles->list();

        $this->assertCount(1, $profiles);
        $this->assertInstanceOf(CredentialProfile::class, $profiles[0]);
        $this->assertEquals('cp_123', $profiles[0]->id);
        $this->assertEquals('F0394148', $profiles[0]->aid);
        $this->assertEquals('Main Office Profile', $profiles[0]->name);
    }

    public function testListCredentialProfilesEmpty(): void
    {
        $this->expectRequest('GET', '/v1/console/credential-profiles', 200, []);

        $profiles = $this->client->console->credentialProfiles->list();

        $this->assertCount(0, $profiles);
    }

    public function testCreateCredentialProfile(): void
    {
        $this->expectRequest('POST', '/v1/console/credential-profiles', 200, [
            'id' => 'cp_new',
            'aid' => 'F0394148',
            'name' => 'Main Office Profile',
            'apple_id' => 'apple_789',
            'created_at' => '2025-06-15T10:00:00Z',
            'card_storage' => 'desfire',
            'keys' => [
                ['label' => 'master', 'value' => 'abc123', 'ex_id' => 'key_1'],
                ['label' => 'read', 'value' => 'def456', 'ex_id' => 'key_2'],
            ],
            'files' => [],
        ]);

        $profile = $this->client->console->credentialProfiles->create([
            'name' => 'Main Office Profile',
            'app_name' => 'KEY-ID-main',
            'keys' => [
                ['value' => 'abc123'],
                ['value' => 'def456'],
            ],
        ]);

        $this->assertInstanceOf(CredentialProfile::class, $profile);
        $this->assertEquals('cp_new', $profile->id);
        $this->assertEquals('F0394148', $profile->aid);
        $this->assertCount(2, $profile->keys);
    }
}
