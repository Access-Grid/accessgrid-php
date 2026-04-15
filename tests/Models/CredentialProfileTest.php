<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\CredentialProfile;

class CredentialProfileTest extends TestCase
{
    public function testConstructionWithFullData(): void
    {
        $data = [
            'id' => 'cp_123',
            'aid' => 'F0394148',
            'name' => 'Main Office Profile',
            'apple_id' => 'apple_456',
            'created_at' => '2025-06-01T12:00:00Z',
            'card_storage' => 'desfire',
            'keys' => [
                ['label' => 'master', 'value' => 'abc123', 'ex_id' => 'key_1'],
            ],
            'files' => [
                ['ex_id' => 'file_1', 'communication_settings' => 'plain'],
            ],
        ];

        $profile = new CredentialProfile($this->client, $data);

        $this->assertEquals('cp_123', $profile->id);
        $this->assertEquals('F0394148', $profile->aid);
        $this->assertEquals('Main Office Profile', $profile->name);
        $this->assertEquals('apple_456', $profile->apple_id);
        $this->assertEquals('apple_456', $profile->appleId);
        $this->assertEquals('2025-06-01T12:00:00Z', $profile->created_at);
        $this->assertEquals('2025-06-01T12:00:00Z', $profile->createdAt);
        $this->assertEquals('desfire', $profile->card_storage);
        $this->assertEquals('desfire', $profile->cardStorage);
        $this->assertCount(1, $profile->keys);
        $this->assertCount(1, $profile->files);
    }

    public function testConstructionWithMinimalData(): void
    {
        $profile = new CredentialProfile($this->client, ['id' => 'cp_456']);

        $this->assertEquals('cp_456', $profile->id);
        $this->assertNull($profile->aid);
        $this->assertNull($profile->name);
        $this->assertNull($profile->apple_id);
        $this->assertNull($profile->card_storage);
        $this->assertEquals([], $profile->keys);
        $this->assertEquals([], $profile->files);
    }
}
