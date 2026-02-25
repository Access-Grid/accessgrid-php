<?php

namespace AccessGrid\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use AccessGrid\AccessGridClient;
use AccessGrid\Http\HttpClientInterface;
use AccessGrid\Http\HttpResponse;

abstract class TestCase extends PHPUnitTestCase
{
    /** @var AccessGridClient */
    protected $client;

    /** @var HttpClientInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $mockHttpClient;

    protected function setUp(): void
    {
        $this->mockHttpClient = $this->createMock(HttpClientInterface::class);
        $this->client = new AccessGridClient(
            'test-account-id',
            'test-secret-key',
            'https://api.accessgrid.com',
            $this->mockHttpClient
        );
    }

    /**
     * Configure the mock to return a response on the next send() call.
     */
    protected function mockResponse(int $status, array $body): void
    {
        $this->mockHttpClient
            ->method('send')
            ->willReturn(new HttpResponse($status, json_encode($body)));
    }

    /**
     * Configure the mock to return a response and assert what was sent.
     */
    protected function expectRequest(string $method, string $urlContains, int $responseStatus, array $responseBody): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo($method),
                $this->stringContains($urlContains),
                $this->isType('array'),
                $this->anything()
            )
            ->willReturn(new HttpResponse($responseStatus, json_encode($responseBody)));
    }
}
