<?php

namespace AccessGrid\Http;

use AccessGrid\Exceptions\AccessGridException;

class CurlHttpClient implements HttpClientInterface
{
    public function send(string $method, string $url, array $headers, ?string $body = null): HttpResponse
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
        ]);

        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new AccessGridException('Request failed: ' . $error);
        }

        return new HttpResponse($httpCode, $responseBody);
    }
}
