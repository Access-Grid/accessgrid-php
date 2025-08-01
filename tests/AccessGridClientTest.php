<?php

namespace AccessGrid\Tests;

use PHPUnit\Framework\TestCase;
use AccessGrid\AccessGridClient;
use AccessGrid\Exceptions\AccessGridException;
use AccessGrid\Exceptions\AuthenticationException;

class AccessGridClientTest extends TestCase
{
    private AccessGridClient $client;

    protected function setUp(): void
    {
        $this->client = new AccessGridClient('test-account-id', 'test-secret-key');
    }

    public function testConstructorThrowsExceptionForEmptyAccountId(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Account ID is required');
        
        new AccessGridClient('', 'secret-key');
    }

    public function testConstructorThrowsExceptionForEmptySecretKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Secret Key is required');
        
        new AccessGridClient('account-id', '');
    }

    public function testGenerateSignature(): void
    {
        $payload = '{"test": "data"}';
        $signature = $this->client->generateSignature($payload);
        
        // Verify it's a valid hex string of the expected length (64 chars for SHA256)
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $signature);
        
        // Test with known values to ensure consistency
        $expectedPayload = '{"id": "test-id"}';
        $sig1 = $this->client->generateSignature($expectedPayload);
        $sig2 = $this->client->generateSignature($expectedPayload);
        
        $this->assertEquals($sig1, $sig2, 'Same payload should generate same signature');
    }

    public function testGenerateSignatureMatchesPythonImplementation(): void
    {
        // Test the exact signature generation logic from Python
        $payload = '{"id": "test-card-id"}';
        
        // This is what the Python implementation does:
        // 1. Base64 encode the payload
        $encodedPayload = base64_encode($payload);
        
        // 2. Generate HMAC-SHA256 with secret key and base64 encoded payload
        $expectedSignature = hash_hmac('sha256', $encodedPayload, 'test-secret-key');
        
        $actualSignature = $this->client->generateSignature($payload);
        
        $this->assertEquals($expectedSignature, $actualSignature);
    }

    public function testGetAccessCards(): void
    {
        $accessCards = $this->client->getAccessCards();
        $this->assertInstanceOf(\AccessGrid\Services\AccessCards::class, $accessCards);
    }

    public function testGetConsole(): void
    {
        $console = $this->client->getConsole();
        $this->assertInstanceOf(\AccessGrid\Services\Console::class, $console);
    }
}