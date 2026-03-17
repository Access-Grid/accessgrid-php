<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\Webhook;

class WebhookTest extends TestCase
{
    public function testConstructWithBearerToken(): void
    {
        $webhook = new Webhook($this->client, [
            'id' => 'wh_abc123',
            'name' => 'My Webhook',
            'url' => 'https://example.com/webhook',
            'auth_method' => 'bearer_token',
            'subscribed_events' => ['ag.access_pass.issued', 'ag.access_pass.activated'],
            'created_at' => '2025-06-01T12:00:00Z',
            'private_key' => 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2',
        ]);

        $this->assertEquals('wh_abc123', $webhook->id);
        $this->assertEquals('My Webhook', $webhook->name);
        $this->assertEquals('https://example.com/webhook', $webhook->url);
        $this->assertEquals('bearer_token', $webhook->authMethod);
        $this->assertCount(2, $webhook->subscribedEvents);
        $this->assertEquals('ag.access_pass.issued', $webhook->subscribedEvents[0]);
        $this->assertEquals('2025-06-01T12:00:00Z', $webhook->createdAt);
        $this->assertEquals('a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', $webhook->privateKey);
        $this->assertNull($webhook->clientCert);
        $this->assertNull($webhook->certExpiresAt);
    }

    public function testConstructWithMtls(): void
    {
        $webhook = new Webhook($this->client, [
            'id' => 'wh_def456',
            'name' => 'mTLS Webhook',
            'url' => 'https://secure.example.com/webhook',
            'auth_method' => 'mtls',
            'subscribed_events' => ['ag.card_template.created'],
            'created_at' => '2025-06-01T12:00:00Z',
            'client_cert' => '-----BEGIN CERTIFICATE-----\nMIIB...\n-----END CERTIFICATE-----',
            'cert_expires_at' => '2025-12-01T12:00:00Z',
        ]);

        $this->assertEquals('wh_def456', $webhook->id);
        $this->assertEquals('mtls', $webhook->authMethod);
        $this->assertEquals('-----BEGIN CERTIFICATE-----\nMIIB...\n-----END CERTIFICATE-----', $webhook->clientCert);
        $this->assertEquals('2025-12-01T12:00:00Z', $webhook->certExpiresAt);
        $this->assertNull($webhook->privateKey);
    }

    public function testConstructWithNullName(): void
    {
        $webhook = new Webhook($this->client, [
            'id' => 'wh_ghi789',
            'name' => null,
            'url' => 'https://example.com/hook',
            'auth_method' => 'bearer_token',
            'subscribed_events' => ['ag.access_pass.issued'],
            'created_at' => '2025-06-01T12:00:00Z',
        ]);

        $this->assertNull($webhook->name);
    }

    public function testConstructWithMinimalData(): void
    {
        $webhook = new Webhook($this->client, []);

        $this->assertNull($webhook->id);
        $this->assertNull($webhook->name);
        $this->assertNull($webhook->url);
        $this->assertNull($webhook->authMethod);
        $this->assertEquals([], $webhook->subscribedEvents);
        $this->assertNull($webhook->createdAt);
        $this->assertNull($webhook->privateKey);
        $this->assertNull($webhook->clientCert);
        $this->assertNull($webhook->certExpiresAt);
    }
}
