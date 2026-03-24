<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\Template;

class TemplateTest extends TestCase
{
    public function testConstructionWithFullData(): void
    {
        $data = [
            'id' => 'tmpl_123',
            'name' => 'Employee Badge',
            'platform' => 'apple',
            'use_case' => 'employee_badge',
            'protocol' => 'desfire',
            'created_at' => '2025-01-01T00:00:00Z',
            'last_published_at' => '2025-06-01T00:00:00Z',
            'issued_keys_count' => 100,
            'active_keys_count' => 85,
            'allowed_device_counts' => ['iphone' => 3, 'watch' => 1],
            'support_settings' => ['support_email' => 'help@example.com'],
            'terms_settings' => ['privacy_policy_url' => 'https://example.com/privacy'],
            'style_settings' => ['background_color' => '#FFFFFF'],
        ];

        $template = new Template($this->client, $data);

        $this->assertEquals('tmpl_123', $template->id);
        $this->assertEquals('Employee Badge', $template->name);
        $this->assertEquals('apple', $template->platform);
        $this->assertEquals('employee_badge', $template->useCase);
        $this->assertEquals('desfire', $template->protocol);
        $this->assertEquals('2025-01-01T00:00:00Z', $template->createdAt);
        $this->assertEquals('2025-06-01T00:00:00Z', $template->lastPublishedAt);
        $this->assertEquals(100, $template->issuedKeysCount);
        $this->assertEquals(85, $template->activeKeysCount);
        $this->assertEquals(['iphone' => 3, 'watch' => 1], $template->allowedDeviceCounts);
        $this->assertEquals('help@example.com', $template->supportSettings['support_email']);
        $this->assertEquals('https://example.com/privacy', $template->termsSettings['privacy_policy_url']);
        $this->assertEquals('#FFFFFF', $template->styleSettings['background_color']);
    }

    public function testConstructionWithMinimalData(): void
    {
        $template = new Template($this->client, [
            'id' => 'tmpl_456',
            'name' => 'Basic Template',
        ]);

        $this->assertEquals('tmpl_456', $template->id);
        $this->assertEquals('Basic Template', $template->name);
        $this->assertNull($template->platform);
        $this->assertNull($template->useCase);
        $this->assertNull($template->protocol);
        $this->assertNull($template->createdAt);
        $this->assertNull($template->lastPublishedAt);
        $this->assertNull($template->issuedKeysCount);
        $this->assertNull($template->activeKeysCount);
    }

    public function testNullableArrayProperties(): void
    {
        $template = new Template($this->client, []);

        $this->assertNull($template->allowedDeviceCounts);
        $this->assertNull($template->supportSettings);
        $this->assertNull($template->termsSettings);
        $this->assertNull($template->styleSettings);
    }
}
