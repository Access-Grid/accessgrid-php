<?php

namespace AccessGrid;

use AccessGrid\Exceptions\AccessGridException;
use AccessGrid\Exceptions\AuthenticationException;
use AccessGrid\Services\AccessCards;
use AccessGrid\Services\Console;

class AccessGridClient
{
    private string $accountId;
    private string $secretKey;
    private string $baseUrl;
    private AccessCards $accessCards;
    private Console $console;

    public function __construct(string $accountId, string $secretKey, string $baseUrl = 'https://api.accessgrid.com')
    {
        if (empty($accountId)) {
            throw new \InvalidArgumentException('Account ID is required');
        }
        
        if (empty($secretKey)) {
            throw new \InvalidArgumentException('Secret Key is required');
        }

        $this->accountId = $accountId;
        $this->secretKey = $secretKey;
        $this->baseUrl = rtrim($baseUrl, '/');
        
        $this->accessCards = new AccessCards($this);
        $this->console = new Console($this);
    }

    public function getAccessCards(): AccessCards
    {
        return $this->accessCards;
    }

    public function getConsole(): Console
    {
        return $this->console;
    }

    /**
     * Generate HMAC signature for the payload according to the shared secret scheme:
     * SHA256.update(shared_secret + base64.encode(payload)).hexdigest()
     * 
     * For requests with no payload (like GET, or actions like suspend/unlink/resume), 
     * caller should provide a payload with {"id": "{resource_id}"}
     */
    public function generateSignature(string $payload): string
    {
        // Base64 encode the payload
        $encodedPayload = base64_encode($payload);
        
        // Create HMAC using the shared secret as the key and the base64 encoded payload as the message
        $signature = hash_hmac('sha256', $encodedPayload, $this->secretKey);
        
        return $signature;
    }

    /**
     * Make an HTTP request to the API
     */
    public function makeRequest(string $method, string $endpoint, ?array $data = null, ?array $params = null): array
    {
        $url = $this->baseUrl . $endpoint;
        
        // Extract resource ID from the endpoint if needed for signature
        $resourceId = null;
        if ($method === 'GET' || ($method === 'POST' && (empty($data) || $data === []))) {
            // Extract the ID from the endpoint - patterns like /resource/{id} or /resource/{id}/action
            $parts = array_filter(explode('/', trim($endpoint, '/')));
            if (count($parts) >= 2) {
                // For actions like unlink/suspend/resume, get the card ID (second to last part)
                $lastPart = end($parts);
                if (in_array($lastPart, ['suspend', 'resume', 'unlink', 'delete'])) {
                    $resourceId = prev($parts);
                } else {
                    // Otherwise, the ID is typically the last part of the path
                    $resourceId = $lastPart;
                }
            }
        }
        
        // Special handling for requests with no payload:
        // 1. POST requests with empty body (like unlink/suspend/resume)
        // 2. GET requests
        if (($method === 'POST' && empty($data)) || $method === 'GET') {
            // For these requests, use {"id": "card_id"} as the payload for signature generation
            if ($resourceId) {
                $payload = json_encode(['id' => $resourceId]);
            } else {
                $payload = '{}';
            }
        } else {
            // For normal POST/PUT/PATCH with body, use the actual payload
            $payload = !empty($data) ? json_encode($data) : '';
        }
        
        // Generate signature
        $signature = $this->generateSignature($payload);
        
        $headers = [
            'X-ACCT-ID: ' . $this->accountId,
            'X-PAYLOAD-SIG: ' . $signature,
            'Content-Type: application/json',
            'User-Agent: accessgrid-php @ v1.0.0'
        ];

        // Initialize cURL
        $ch = curl_init();
        
        // Set basic cURL options
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method
        ]);

        // For requests with empty bodies (GET or action endpoints like unlink/suspend/resume),
        // we need to include the sig_payload parameter
        if ($method === 'GET' || ($method === 'POST' && empty($data))) {
            if ($params === null) {
                $params = [];
            }
            // Include the ID payload in the query params
            if ($resourceId) {
                // The server expects the raw JSON string, not URL-encoded
                $params['sig_payload'] = json_encode(['id' => $resourceId]);
            }
        }
        
        // Add query parameters for GET requests or when params are provided
        if (!empty($params)) {
            $queryString = http_build_query($params);
            $separator = strpos($url, '?') !== false ? '&' : '?';
            curl_setopt($ch, CURLOPT_URL, $url . $separator . $queryString);
        }
        
        // For POST/PUT/PATCH with data, set the JSON body
        if (!empty($data) && $method !== 'GET') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new AccessGridException('Request failed: ' . $error);
        }
        
        if ($httpCode === 401) {
            throw new AuthenticationException('Invalid credentials');
        } elseif ($httpCode === 402) {
            throw new AccessGridException('Insufficient account balance');
        } elseif ($httpCode < 200 || $httpCode >= 300) {
            $errorData = json_decode($response, true) ?: [];
            $errorMessage = $errorData['message'] ?? $response;
            throw new AccessGridException('API request failed: ' . $errorMessage);
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new AccessGridException('Invalid JSON response: ' . json_last_error_msg());
        }

        return $decoded;
    }

    public function get(string $endpoint, ?array $params = null): array
    {
        return $this->makeRequest('GET', $endpoint, null, $params);
    }

    public function post(string $endpoint, array $data): array
    {
        return $this->makeRequest('POST', $endpoint, $data);
    }

    public function put(string $endpoint, array $data): array
    {
        return $this->makeRequest('PUT', $endpoint, $data);
    }

    public function patch(string $endpoint, array $data): array
    {
        return $this->makeRequest('PATCH', $endpoint, $data);
    }
}