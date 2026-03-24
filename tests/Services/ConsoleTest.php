<?php

namespace AccessGrid\Tests\Services;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\Template;
use AccessGrid\Models\PassTemplatePair;
use AccessGrid\Models\TemplateInfo;
use AccessGrid\Models\LedgerItem;
use AccessGrid\Models\LedgerItemAccessPass;
use AccessGrid\Models\LedgerItemPassTemplate;
use AccessGrid\Models\Webhook;

class ConsoleTest extends TestCase
{
    public function testCreateTemplate(): void
    {
        $this->expectRequest('POST', '/v1/console/card-templates', 200, [
            'id' => 'tmpl_123',
            'name' => 'Employee Badge',
            'platform' => 'apple',
            'protocol' => 'desfire',
        ]);

        $template = $this->client->console->createTemplate([
            'name' => 'Employee Badge',
            'platform' => 'apple',
            'use_case' => 'employee_badge',
            'protocol' => 'desfire',
        ]);

        $this->assertInstanceOf(Template::class, $template);
        $this->assertEquals('tmpl_123', $template->id);
        $this->assertEquals('Employee Badge', $template->name);
        $this->assertEquals('apple', $template->platform);
    }

    public function testUpdateTemplate(): void
    {
        $this->expectRequest('PUT', '/v1/console/card-templates/tmpl_123', 200, [
            'id' => 'tmpl_123',
            'name' => 'Updated Badge',
        ]);

        $template = $this->client->console->updateTemplate([
            'card_template_id' => 'tmpl_123',
            'name' => 'Updated Badge',
        ]);

        $this->assertInstanceOf(Template::class, $template);
        $this->assertEquals('Updated Badge', $template->name);
    }

    public function testReadTemplate(): void
    {
        $this->expectRequest('GET', '/v1/console/card-templates/tmpl_123', 200, [
            'id' => 'tmpl_123',
            'name' => 'Employee Badge',
            'platform' => 'apple',
            'protocol' => 'desfire',
            'use_case' => 'employee_badge',
            'created_at' => '2025-01-01T00:00:00Z',
            'last_published_at' => '2025-06-01T00:00:00Z',
            'issued_keys_count' => 100,
            'active_keys_count' => 85,
            'allowed_device_counts' => ['iphone' => 3, 'watch' => 1],
            'support_settings' => ['support_email' => 'help@example.com'],
            'terms_settings' => ['privacy_policy_url' => 'https://example.com/privacy'],
            'style_settings' => ['background_color' => '#FFFFFF'],
        ]);

        $template = $this->client->console->readTemplate([
            'card_template_id' => 'tmpl_123',
        ]);

        $this->assertInstanceOf(Template::class, $template);
        $this->assertEquals('tmpl_123', $template->id);
        $this->assertEquals('employee_badge', $template->useCase);
        $this->assertEquals(100, $template->issuedKeysCount);
        $this->assertEquals(85, $template->activeKeysCount);
        $this->assertEquals('#FFFFFF', $template->styleSettings['background_color']);
    }

    public function testGetLogs(): void
    {
        $this->expectRequest('GET', '/v1/console/card-templates/tmpl_123/logs', 200, [
            'logs' => [
                ['event' => 'access_pass.device_added', 'created_at' => '2025-06-01T12:00:00Z'],
                ['event' => 'access_pass.device_removed', 'created_at' => '2025-06-02T12:00:00Z'],
            ],
            'pagination' => ['current_page' => 1, 'total_pages' => 3],
        ]);

        $result = $this->client->console->getLogs('tmpl_123');

        $this->assertArrayHasKey('logs', $result);
        $this->assertCount(2, $result['logs']);
        $this->assertEquals('access_pass.device_added', $result['logs'][0]['event']);
    }

    public function testGetLogsWithParams(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('GET'),
                $this->callback(function (string $url) {
                    return strpos($url, '/v1/console/card-templates/tmpl_123/logs') !== false
                        && strpos($url, 'device=mobile') !== false;
                }),
                $this->anything(),
                $this->anything()
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, json_encode([
                'logs' => [],
                'pagination' => ['current_page' => 1, 'total_pages' => 1],
            ])));

        $result = $this->client->console->getLogs('tmpl_123', ['device' => 'mobile']);

        $this->assertArrayHasKey('logs', $result);
    }

    public function testEventLog(): void
    {
        $this->mockResponse(200, [
            'events' => [
                ['event' => 'access_pass.device_added', 'created_at' => '2025-06-01T12:00:00Z', 'ip_address' => '1.2.3.4'],
                ['event' => 'access_pass.suspended', 'created_at' => '2025-06-02T12:00:00Z', 'ip_address' => '5.6.7.8'],
            ],
        ]);

        $events = $this->client->console->eventLog([
            'card_template_id' => 'tmpl_123',
        ]);

        $this->assertCount(2, $events);
        $this->assertIsObject($events[0]);
        $this->assertEquals('access_pass.device_added', $events[0]->event);
        $this->assertEquals('1.2.3.4', $events[0]->ip_address);
    }

    public function testEventLogWithFilters(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('GET'),
                $this->callback(function (string $url) {
                    return strpos($url, 'event_type=access_pass.device_added') !== false;
                }),
                $this->anything(),
                $this->anything()
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, json_encode([
                'events' => [
                    ['event' => 'access_pass.device_added', 'created_at' => '2025-06-01T12:00:00Z'],
                ],
            ])));

        $events = $this->client->console->eventLog([
            'card_template_id' => 'tmpl_123',
            'filters' => ['event_type' => 'access_pass.device_added'],
        ]);

        $this->assertCount(1, $events);
    }

    public function testIosPreflight(): void
    {
        $this->expectRequest('POST', '/v1/console/ios-preflight', 200, [
            'provisioningCredentialIdentifier' => 'prov_123',
            'sharingInstanceIdentifier' => 'share_456',
            'cardTemplateIdentifier' => 'tmpl_123',
            'environmentIdentifier' => 'production',
        ]);

        $result = $this->client->console->iosPreflight([
            'card_template_id' => 'tmpl_123',
            'access_pass_ex_id' => 'pass_789',
        ]);

        $this->assertIsObject($result);
        $this->assertEquals('prov_123', $result->provisioningCredentialIdentifier);
        $this->assertEquals('share_456', $result->sharingInstanceIdentifier);
        $this->assertEquals('production', $result->environmentIdentifier);
    }

    public function testListPassTemplatePairs(): void
    {
        $this->expectRequest('GET', '/v1/console/pass-template-pairs', 200, [
            'pass_template_pairs' => [
                [
                    'id' => 'pair_123',
                    'name' => 'Employee Badge Pair',
                    'created_at' => '2025-01-01T00:00:00Z',
                    'android_template' => [
                        'id' => 'tmpl_android_456',
                        'name' => 'Employee Badge Android',
                        'platform' => 'android',
                    ],
                    'ios_template' => [
                        'id' => 'tmpl_ios_789',
                        'name' => 'Employee Badge iOS',
                        'platform' => 'apple',
                    ],
                ],
            ],
            'pagination' => [
                'current_page' => 1,
                'per_page' => 25,
                'total_pages' => 1,
                'total_count' => 1,
            ],
        ]);

        $result = $this->client->console->listPassTemplatePairs();

        $this->assertArrayHasKey('pass_template_pairs', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(1, $result['pass_template_pairs']);

        $pair = $result['pass_template_pairs'][0];
        $this->assertInstanceOf(PassTemplatePair::class, $pair);
        $this->assertEquals('pair_123', $pair->id);
        $this->assertEquals('Employee Badge Pair', $pair->name);

        $this->assertInstanceOf(TemplateInfo::class, $pair->androidTemplate);
        $this->assertEquals('tmpl_android_456', $pair->androidTemplate->id);

        $this->assertInstanceOf(TemplateInfo::class, $pair->iosTemplate);
        $this->assertEquals('tmpl_ios_789', $pair->iosTemplate->id);

        $this->assertEquals(1, $result['pagination']['current_page']);
    }

    public function testListPassTemplatePairsWithParams(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('GET'),
                $this->callback(function (string $url) {
                    return strpos($url, '/v1/console/pass-template-pairs') !== false
                        && strpos($url, 'page=2') !== false
                        && strpos($url, 'per_page=10') !== false;
                }),
                $this->anything(),
                $this->anything()
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, json_encode([
                'pass_template_pairs' => [],
                'pagination' => ['current_page' => 2, 'per_page' => 10, 'total_pages' => 3, 'total_count' => 25],
            ])));

        $result = $this->client->console->listPassTemplatePairs(['page' => 2, 'per_page' => 10]);

        $this->assertArrayHasKey('pass_template_pairs', $result);
        $this->assertCount(0, $result['pass_template_pairs']);
        $this->assertEquals(2, $result['pagination']['current_page']);
    }

    public function testListPassTemplatePairsEmpty(): void
    {
        $this->expectRequest('GET', '/v1/console/pass-template-pairs', 200, [
            'pass_template_pairs' => [],
            'pagination' => ['current_page' => 1, 'per_page' => 25, 'total_pages' => 0, 'total_count' => 0],
        ]);

        $result = $this->client->console->listPassTemplatePairs();

        $this->assertCount(0, $result['pass_template_pairs']);
    }

    public function testListLedgerItems(): void
    {
        $this->expectRequest('GET', '/v1/console/ledger-items', 200, [
            'ledger_items' => [
                [
                    'created_at' => '2025-06-15T10:30:00Z',
                    'amount' => 1.50,
                    'id' => 'li_123',
                    'ex_id' => 'li_123',
                    'kind' => 'access_pass_issued',
                    'metadata' => ['access_pass_ex_id' => 'pass_456'],
                    'access_pass' => [
                        'id' => 'pass_456',
                        'ex_id' => 'pass_456',
                        'full_name' => 'Jane Doe',
                        'state' => 'active',
                        'metadata' => [],
                        'pass_template' => [
                            'id' => 'tmpl_abc',
                            'ex_id' => 'tmpl_abc',
                            'name' => 'Employee Badge',
                            'protocol' => 'desfire',
                            'platform' => 'apple',
                            'use_case' => 'employee_badge',
                        ],
                    ],
                ],
            ],
            'pagination' => [
                'current_page' => 1,
                'per_page' => 50,
                'total_pages' => 1,
                'total_count' => 1,
            ],
        ]);

        $result = $this->client->console->listLedgerItems();

        $this->assertArrayHasKey('ledger_items', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(1, $result['ledger_items']);

        $item = $result['ledger_items'][0];
        $this->assertInstanceOf(LedgerItem::class, $item);
        $this->assertEquals('li_123', $item->id);
        $this->assertEquals(1.50, $item->amount);
        $this->assertEquals('access_pass_issued', $item->kind);

        $this->assertInstanceOf(LedgerItemAccessPass::class, $item->accessPass);
        $this->assertEquals('pass_456', $item->accessPass->id);
        $this->assertEquals('Jane Doe', $item->accessPass->fullName);

        $this->assertInstanceOf(LedgerItemPassTemplate::class, $item->accessPass->passTemplate);
        $this->assertEquals('tmpl_abc', $item->accessPass->passTemplate->id);
        $this->assertEquals('Employee Badge', $item->accessPass->passTemplate->name);

        $this->assertEquals(1, $result['pagination']['current_page']);
    }

    public function testListLedgerItemsWithParams(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('GET'),
                $this->callback(function (string $url) {
                    return strpos($url, '/v1/console/ledger-items') !== false
                        && strpos($url, 'page=2') !== false
                        && strpos($url, 'per_page=10') !== false
                        && strpos($url, 'start_date=2025-01-01T00%3A00%3A00Z') !== false
                        && strpos($url, 'end_date=2025-06-30T23%3A59%3A59Z') !== false;
                }),
                $this->anything(),
                $this->anything()
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, json_encode([
                'ledger_items' => [],
                'pagination' => ['current_page' => 2, 'per_page' => 10, 'total_pages' => 5, 'total_count' => 42],
            ])));

        $result = $this->client->console->listLedgerItems([
            'page' => 2,
            'per_page' => 10,
            'start_date' => '2025-01-01T00:00:00Z',
            'end_date' => '2025-06-30T23:59:59Z',
        ]);

        $this->assertArrayHasKey('ledger_items', $result);
        $this->assertCount(0, $result['ledger_items']);
        $this->assertEquals(2, $result['pagination']['current_page']);
    }

    public function testListLedgerItemsWithNullAccessPass(): void
    {
        $this->expectRequest('GET', '/v1/console/ledger-items', 200, [
            'ledger_items' => [
                [
                    'created_at' => '2025-06-15T10:30:00Z',
                    'amount' => 0.75,
                    'id' => 'li_no_pass',
                    'ex_id' => 'li_no_pass',
                    'kind' => 'adjustment',
                    'metadata' => [],
                    'access_pass' => null,
                ],
            ],
            'pagination' => ['current_page' => 1, 'per_page' => 50, 'total_pages' => 1, 'total_count' => 1],
        ]);

        $result = $this->client->console->listLedgerItems();

        $item = $result['ledger_items'][0];
        $this->assertInstanceOf(LedgerItem::class, $item);
        $this->assertNull($item->accessPass);
    }

    public function testListLedgerItemsEmpty(): void
    {
        $this->expectRequest('GET', '/v1/console/ledger-items', 200, [
            'ledger_items' => [],
            'pagination' => ['current_page' => 1, 'per_page' => 50, 'total_pages' => 0, 'total_count' => 0],
        ]);

        $result = $this->client->console->listLedgerItems();

        $this->assertCount(0, $result['ledger_items']);
    }

    // --- Webhooks ---

    public function testListWebhooks(): void
    {
        $this->expectRequest('GET', '/v1/console/webhooks', 200, [
            'webhooks' => [
                [
                    'id' => 'wh_abc123',
                    'name' => 'My Webhook',
                    'url' => 'https://example.com/webhook',
                    'auth_method' => 'bearer_token',
                    'subscribed_events' => ['ag.access_pass.issued', 'ag.access_pass.activated'],
                    'created_at' => '2025-06-01T12:00:00Z',
                ],
                [
                    'id' => 'wh_def456',
                    'name' => 'mTLS Webhook',
                    'url' => 'https://secure.example.com/webhook',
                    'auth_method' => 'mtls',
                    'subscribed_events' => ['ag.card_template.created'],
                    'created_at' => '2025-06-02T12:00:00Z',
                    'cert_expires_at' => '2025-12-02T12:00:00Z',
                ],
            ],
            'pagination' => [
                'current_page' => 1,
                'per_page' => 50,
                'total_pages' => 1,
                'total_count' => 2,
            ],
        ]);

        $result = $this->client->console->listWebhooks();

        $this->assertArrayHasKey('webhooks', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(2, $result['webhooks']);

        $wh = $result['webhooks'][0];
        $this->assertInstanceOf(Webhook::class, $wh);
        $this->assertEquals('wh_abc123', $wh->id);
        $this->assertEquals('My Webhook', $wh->name);
        $this->assertEquals('bearer_token', $wh->authMethod);
        $this->assertCount(2, $wh->subscribedEvents);

        $wh2 = $result['webhooks'][1];
        $this->assertInstanceOf(Webhook::class, $wh2);
        $this->assertEquals('mtls', $wh2->authMethod);
        $this->assertEquals('2025-12-02T12:00:00Z', $wh2->certExpiresAt);

        $this->assertEquals(1, $result['pagination']['current_page']);
        $this->assertEquals(2, $result['pagination']['total_count']);
    }

    public function testListWebhooksWithPagination(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('GET'),
                $this->callback(function (string $url) {
                    return strpos($url, '/v1/console/webhooks') !== false
                        && strpos($url, 'page=2') !== false
                        && strpos($url, 'per_page=10') !== false;
                }),
                $this->anything(),
                $this->anything()
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, json_encode([
                'webhooks' => [],
                'pagination' => ['current_page' => 2, 'per_page' => 10, 'total_pages' => 3, 'total_count' => 25],
            ])));

        $result = $this->client->console->listWebhooks(['page' => 2, 'per_page' => 10]);

        $this->assertCount(0, $result['webhooks']);
        $this->assertEquals(2, $result['pagination']['current_page']);
    }

    public function testCreateWebhookBearerToken(): void
    {
        $this->expectRequest('POST', '/v1/console/webhooks', 201, [
            'id' => 'wh_new123',
            'name' => 'New Webhook',
            'url' => 'https://example.com/hook',
            'auth_method' => 'bearer_token',
            'subscribed_events' => ['ag.access_pass.issued'],
            'created_at' => '2025-06-15T10:00:00Z',
            'private_key' => 'a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2',
        ]);

        $webhook = $this->client->console->createWebhook([
            'name' => 'New Webhook',
            'url' => 'https://example.com/hook',
            'auth_method' => 'bearer_token',
            'subscribed_events' => ['ag.access_pass.issued'],
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals('wh_new123', $webhook->id);
        $this->assertEquals('bearer_token', $webhook->authMethod);
        $this->assertEquals('a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2c3d4e5f6a1b2', $webhook->privateKey);
        $this->assertNull($webhook->clientCert);
    }

    public function testCreateWebhookMtls(): void
    {
        $this->expectRequest('POST', '/v1/console/webhooks', 201, [
            'id' => 'wh_mtls123',
            'name' => 'Secure Webhook',
            'url' => 'https://secure.example.com/hook',
            'auth_method' => 'mtls',
            'subscribed_events' => ['ag.access_pass.issued', 'ag.access_pass.deleted'],
            'created_at' => '2025-06-15T10:00:00Z',
            'client_cert' => '-----BEGIN CERTIFICATE-----\nMIIB...\n-----END CERTIFICATE-----',
            'cert_expires_at' => '2025-12-15T10:00:00Z',
        ]);

        $webhook = $this->client->console->createWebhook([
            'name' => 'Secure Webhook',
            'url' => 'https://secure.example.com/hook',
            'auth_method' => 'mtls',
            'subscribed_events' => ['ag.access_pass.issued', 'ag.access_pass.deleted'],
        ]);

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertEquals('mtls', $webhook->authMethod);
        $this->assertEquals('-----BEGIN CERTIFICATE-----\nMIIB...\n-----END CERTIFICATE-----', $webhook->clientCert);
        $this->assertEquals('2025-12-15T10:00:00Z', $webhook->certExpiresAt);
        $this->assertNull($webhook->privateKey);
    }

    public function testDeleteWebhook(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('DELETE'),
                $this->stringContains('/v1/console/webhooks/wh_abc123'),
                $this->isType('array'),
                $this->anything()
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(204, ''));

        $this->client->console->deleteWebhook('wh_abc123');
    }
}
