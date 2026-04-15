<?php

namespace AccessGrid\Tests\Models;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\LandingPage;

class LandingPageTest extends TestCase
{
    public function testConstructionWithFullData(): void
    {
        $data = [
            'id' => 'lp_123',
            'name' => 'Miami Office',
            'kind' => 'universal',
            'password_protected' => true,
            'logo_url' => 'https://example.com/logo.png',
            'created_at' => '2025-06-01T12:00:00Z',
        ];

        $page = new LandingPage($this->client, $data);

        $this->assertEquals('lp_123', $page->id);
        $this->assertEquals('Miami Office', $page->name);
        $this->assertEquals('universal', $page->kind);
        $this->assertTrue($page->password_protected);
        $this->assertTrue($page->passwordProtected);
        $this->assertEquals('https://example.com/logo.png', $page->logo_url);
        $this->assertEquals('https://example.com/logo.png', $page->logoUrl);
        $this->assertEquals('2025-06-01T12:00:00Z', $page->created_at);
        $this->assertEquals('2025-06-01T12:00:00Z', $page->createdAt);
    }

    public function testConstructionWithMinimalData(): void
    {
        $page = new LandingPage($this->client, ['id' => 'lp_456']);

        $this->assertEquals('lp_456', $page->id);
        $this->assertNull($page->name);
        $this->assertNull($page->kind);
        $this->assertNull($page->password_protected);
        $this->assertNull($page->logo_url);
        $this->assertNull($page->created_at);
    }
}
