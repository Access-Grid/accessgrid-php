<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\AccessCard;

class AccessCardTest extends TestCase
{
    public function testConstructionWithFullData(): void
    {
        $data = [
            'id' => 'card_123',
            'install_url' => 'https://example.com/install',
            'state' => 'active',
            'full_name' => 'John Doe',
            'expiration_date' => '2026-12-31',
            'card_number' => '12345',
            'site_code' => 'SC01',
            'file_data' => 'base64data',
            'direct_install_url' => 'https://example.com/direct',
            'details' => ['foo' => 'bar'],
            'devices' => [['device_id' => 'dev_1']],
            'metadata' => ['key' => 'value'],
        ];

        $card = new AccessCard($this->client, $data);

        $this->assertEquals('card_123', $card->id);
        $this->assertEquals('https://example.com/install', $card->install_url);
        $this->assertEquals('https://example.com/install', $card->url);
        $this->assertEquals('active', $card->state);
        $this->assertEquals('John Doe', $card->full_name);
        $this->assertEquals('2026-12-31', $card->expiration_date);
        $this->assertEquals('12345', $card->card_number);
        $this->assertEquals('SC01', $card->site_code);
        $this->assertEquals('base64data', $card->file_data);
        $this->assertEquals('https://example.com/direct', $card->direct_install_url);
        $this->assertEquals(['foo' => 'bar'], $card->details);
        $this->assertCount(1, $card->devices);
        $this->assertEquals('value', $card->metadata['key']);
    }

    public function testCamelCaseAndSnakeCaseProperties(): void
    {
        $data = [
            'full_name' => 'Jane Doe',
            'expiration_date' => '2027-01-01',
            'card_number' => '67890',
            'site_code' => 'SC02',
            'file_data' => 'somedata',
            'direct_install_url' => 'https://example.com/direct2',
        ];

        $card = new AccessCard($this->client, $data);

        $this->assertEquals($card->full_name, $card->fullName);
        $this->assertEquals($card->expiration_date, $card->expirationDate);
        $this->assertEquals($card->card_number, $card->cardNumber);
        $this->assertEquals($card->site_code, $card->siteCode);
        $this->assertEquals($card->file_data, $card->fileData);
        $this->assertEquals($card->direct_install_url, $card->directInstallUrl);
    }

    public function testConstructionWithMinimalData(): void
    {
        $card = new AccessCard($this->client, ['id' => 'card_456']);

        $this->assertEquals('card_456', $card->id);
        $this->assertNull($card->state);
        $this->assertNull($card->full_name);
        $this->assertNull($card->install_url);
        $this->assertNull($card->expiration_date);
        $this->assertNull($card->card_number);
        $this->assertNull($card->site_code);
        $this->assertNull($card->file_data);
        $this->assertNull($card->direct_install_url);
        $this->assertNull($card->details);
    }

    public function testDefaultDevicesAndMetadata(): void
    {
        $card = new AccessCard($this->client, []);

        $this->assertEquals([], $card->devices);
        $this->assertEquals([], $card->metadata);
    }

    public function testToString(): void
    {
        $card = new AccessCard($this->client, [
            'id' => 'card_123',
            'full_name' => 'John Doe',
            'state' => 'active',
        ]);

        $this->assertEquals("AccessCard(name='John Doe', id='card_123', state='active')", (string) $card);
    }

    public function testToStringWithNulls(): void
    {
        $card = new AccessCard($this->client, []);

        $this->assertEquals("AccessCard(name='', id='', state='')", (string) $card);
    }
}
