<?php

namespace AccessGrid\Tests;

use AccessGrid\AccessGridClient;
use AccessGrid\Exceptions\AccessGridException;
use AccessGrid\Exceptions\AuthenticationException;

class AccessGridClientTest extends TestCase
{

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

    public function testMakeRequestReturnsDecodedJson(): void
    {
        $this->mockResponse(200, ['id' => 'card_123', 'state' => 'active']);

        $result = $this->client->get('/v1/key-cards/card_123');

        $this->assertEquals('card_123', $result['id']);
        $this->assertEquals('active', $result['state']);
    }

    public function testMakeRequestThrowsAuthenticationExceptionOn401(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->mockResponse(401, ['message' => 'Unauthorized']);

        $this->client->get('/v1/key-cards/card_123');
    }

    public function testMakeRequestThrowsAccessGridExceptionOn402(): void
    {
        $this->expectException(AccessGridException::class);
        $this->expectExceptionMessage('Insufficient account balance');

        $this->mockResponse(402, ['message' => 'Insufficient account balance']);

        $this->client->get('/v1/key-cards/card_123');
    }

    public function testMakeRequestThrowsAccessGridExceptionOn500(): void
    {
        $this->expectException(AccessGridException::class);
        $this->expectExceptionMessage('API request failed');

        $this->mockResponse(500, ['message' => 'Internal server error']);

        $this->client->get('/v1/key-cards/card_123');
    }

    public function testMakeRequestThrowsAccessGridExceptionOnInvalidJson(): void
    {
        $this->expectException(AccessGridException::class);
        $this->expectExceptionMessage('Invalid JSON response');

        $this->mockHttpClient
            ->method('send')
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, 'not valid json'));

        $this->client->get('/v1/key-cards/card_123');
    }

    public function testMakeRequestIncludesCorrectHeaders(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(function (array $headers) {
                    $headerString = implode("\n", $headers);
                    return strpos($headerString, 'X-ACCT-ID: test-account-id') !== false
                        && strpos($headerString, 'X-PAYLOAD-SIG: ') !== false
                        && strpos($headerString, 'Content-Type: application/json') !== false
                        && strpos($headerString, 'User-Agent: accessgrid-php') !== false;
                }),
                $this->anything()
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, '{"ok":true}'));

        $this->client->get('/v1/key-cards/card_123');
    }

    public function testPostSendsJsonBody(): void
    {
        $data = ['full_name' => 'John Doe', 'card_template_id' => 'tmpl_1'];

        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('POST'),
                $this->anything(),
                $this->anything(),
                $this->equalTo(json_encode($data))
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, '{"id":"card_123"}'));

        $this->client->post('/v1/key-cards', $data);
    }

    public function testGetAppendsQueryParams(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('send')
            ->with(
                $this->equalTo('GET'),
                $this->stringContains('template_id=tmpl_1'),
                $this->anything(),
                $this->anything()
            )
            ->willReturn(new \AccessGrid\Http\HttpResponse(200, '{"keys":[]}'));

        $this->client->get('/v1/key-cards', ['template_id' => 'tmpl_1']);
    }
}