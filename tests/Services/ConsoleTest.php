<?php

namespace AccessGrid\Tests\Services;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\Template;

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
}
