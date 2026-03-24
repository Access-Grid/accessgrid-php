<?php

namespace AccessGrid\Tests\Services;

use AccessGrid\Tests\TestCase;
use AccessGrid\Models\AccessCard;

class AccessCardsTest extends TestCase
{
    public function testIssue(): void
    {
        $this->expectRequest('POST', '/v1/key-cards', 200, [
            'id' => 'card_123',
            'state' => 'active',
            'install_url' => 'https://example.com/install',
        ]);

        $card = $this->client->accessCards->issue([
            'card_template_id' => 'tmpl_1',
            'full_name' => 'John Doe',
        ]);

        $this->assertInstanceOf(AccessCard::class, $card);
        $this->assertEquals('card_123', $card->id);
        $this->assertEquals('active', $card->state);
        $this->assertEquals('https://example.com/install', $card->install_url);
    }

    public function testProvisionDelegatesToIssue(): void
    {
        $this->expectRequest('POST', '/v1/key-cards', 200, [
            'id' => 'card_456',
            'state' => 'active',
        ]);

        $card = $this->client->accessCards->provision([
            'card_template_id' => 'tmpl_1',
            'full_name' => 'Jane Doe',
        ]);

        $this->assertInstanceOf(AccessCard::class, $card);
        $this->assertEquals('card_456', $card->id);
    }

    public function testGet(): void
    {
        $this->expectRequest('GET', '/v1/key-cards/card_123', 200, [
            'id' => 'card_123',
            'state' => 'active',
            'full_name' => 'John Doe',
            'expiration_date' => '2026-12-31',
            'card_number' => '12345',
            'site_code' => 'SC01',
            'devices' => [['device_id' => 'dev_1']],
            'metadata' => ['key' => 'value'],
        ]);

        $card = $this->client->accessCards->get('card_123');

        $this->assertInstanceOf(AccessCard::class, $card);
        $this->assertEquals('card_123', $card->id);
        $this->assertEquals('John Doe', $card->full_name);
        $this->assertEquals('2026-12-31', $card->expiration_date);
        $this->assertEquals('12345', $card->card_number);
        $this->assertEquals('SC01', $card->site_code);
        $this->assertCount(1, $card->devices);
        $this->assertEquals('value', $card->metadata['key']);
    }

    public function testUpdate(): void
    {
        $this->expectRequest('PATCH', '/v1/key-cards/card_123', 200, [
            'id' => 'card_123',
            'full_name' => 'Jane Updated',
            'state' => 'active',
        ]);

        $card = $this->client->accessCards->update([
            'card_id' => 'card_123',
            'full_name' => 'Jane Updated',
        ]);

        $this->assertInstanceOf(AccessCard::class, $card);
        $this->assertEquals('Jane Updated', $card->full_name);
    }

    public function testList(): void
    {
        $this->expectRequest('GET', '/v1/key-cards', 200, [
            'keys' => [
                ['id' => 'card_1', 'state' => 'active'],
                ['id' => 'card_2', 'state' => 'active'],
            ],
        ]);

        $cards = $this->client->accessCards->list('tmpl_1');

        $this->assertCount(2, $cards);
        $this->assertInstanceOf(AccessCard::class, $cards[0]);
        $this->assertEquals('card_1', $cards[0]->id);
        $this->assertEquals('card_2', $cards[1]->id);
    }

    public function testListWithStateFilter(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('GET'),
                $this->callback(function (string $url) {
                    return strpos($url, 'template_id=tmpl_1') !== false
                        && strpos($url, 'state=suspended') !== false;
                }),
                $this->anything(),
                $this->anything()
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, json_encode([
                'keys' => [['id' => 'card_3', 'state' => 'suspended']],
            ])));

        $cards = $this->client->accessCards->list('tmpl_1', 'suspended');

        $this->assertCount(1, $cards);
        $this->assertEquals('suspended', $cards[0]->state);
    }

    public function testListEmptyResponse(): void
    {
        $this->mockResponse(200, ['keys' => []]);

        $cards = $this->client->accessCards->list('tmpl_1');

        $this->assertCount(0, $cards);
    }

    public function testSuspend(): void
    {
        $this->expectRequest('POST', '/v1/key-cards/card_123/suspend', 200, [
            'id' => 'card_123',
            'state' => 'suspended',
        ]);

        $card = $this->client->accessCards->suspend(['card_id' => 'card_123']);

        $this->assertInstanceOf(AccessCard::class, $card);
        $this->assertEquals('suspended', $card->state);
    }

    public function testResume(): void
    {
        $this->expectRequest('POST', '/v1/key-cards/card_123/resume', 200, [
            'id' => 'card_123',
            'state' => 'active',
        ]);

        $card = $this->client->accessCards->resume(['card_id' => 'card_123']);

        $this->assertInstanceOf(AccessCard::class, $card);
        $this->assertEquals('active', $card->state);
    }

    public function testUnlink(): void
    {
        $this->expectRequest('POST', '/v1/key-cards/card_123/unlink', 200, [
            'id' => 'card_123',
            'state' => 'unlinked',
        ]);

        $card = $this->client->accessCards->unlink(['card_id' => 'card_123']);

        $this->assertInstanceOf(AccessCard::class, $card);
        $this->assertEquals('unlinked', $card->state);
    }

    public function testDelete(): void
    {
        $this->expectRequest('POST', '/v1/key-cards/card_123/delete', 200, [
            'id' => 'card_123',
            'state' => 'deleted',
        ]);

        $card = $this->client->accessCards->delete(['card_id' => 'card_123']);

        $this->assertInstanceOf(AccessCard::class, $card);
        $this->assertEquals('deleted', $card->state);
    }
}
