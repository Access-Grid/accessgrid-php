<?php

namespace AccessGrid\Tests\Services;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\LandingPage;

class LandingPagesTest extends TestCase
{
    public function testListLandingPages(): void
    {
        $this->expectRequest('GET', '/v1/console/landing-pages', 200, [
            [
                'id' => 'lp_123',
                'name' => 'Miami Office',
                'kind' => 'universal',
                'password_protected' => false,
                'logo_url' => null,
                'created_at' => '2025-06-01T12:00:00Z',
            ],
        ]);

        $pages = $this->client->console->listLandingPages();

        $this->assertCount(1, $pages);
        $this->assertInstanceOf(LandingPage::class, $pages[0]);
        $this->assertEquals('lp_123', $pages[0]->id);
        $this->assertEquals('Miami Office', $pages[0]->name);
        $this->assertEquals('universal', $pages[0]->kind);
    }

    public function testListLandingPagesEmpty(): void
    {
        $this->expectRequest('GET', '/v1/console/landing-pages', 200, []);

        $pages = $this->client->console->listLandingPages();

        $this->assertCount(0, $pages);
    }

    public function testCreateLandingPage(): void
    {
        $this->expectRequest('POST', '/v1/console/landing-pages', 200, [
            'id' => 'lp_new',
            'name' => 'Miami Office Access Pass',
            'kind' => 'universal',
            'password_protected' => false,
            'created_at' => '2025-06-15T10:00:00Z',
        ]);

        $page = $this->client->console->createLandingPage([
            'name' => 'Miami Office Access Pass',
            'kind' => 'universal',
            'additional_text' => 'Welcome to the Miami Office',
            'bg_color' => '#f1f5f9',
            'allow_immediate_download' => true,
        ]);

        $this->assertInstanceOf(LandingPage::class, $page);
        $this->assertEquals('lp_new', $page->id);
        $this->assertEquals('Miami Office Access Pass', $page->name);
    }

    public function testUpdateLandingPage(): void
    {
        $this->expectRequest('PUT', '/v1/console/landing-pages/lp_123', 200, [
            'id' => 'lp_123',
            'name' => 'Updated Miami Office',
            'kind' => 'universal',
            'password_protected' => false,
            'created_at' => '2025-06-01T12:00:00Z',
        ]);

        $page = $this->client->console->updateLandingPage('lp_123', [
            'name' => 'Updated Miami Office',
            'additional_text' => 'Welcome!',
            'bg_color' => '#e2e8f0',
        ]);

        $this->assertInstanceOf(LandingPage::class, $page);
        $this->assertEquals('lp_123', $page->id);
        $this->assertEquals('Updated Miami Office', $page->name);
    }
}
