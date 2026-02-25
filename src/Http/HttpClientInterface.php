<?php

namespace AccessGrid\Http;

interface HttpClientInterface
{
    /**
     * Send an HTTP request and return the response.
     *
     * @param string      $method  HTTP method (GET, POST, PUT, PATCH)
     * @param string      $url     Fully-qualified URL including query string
     * @param array       $headers Headers in "Key: Value" format
     * @param string|null $body    Request body (JSON string), null for bodyless requests
     * @return HttpResponse
     * @throws \AccessGrid\Exceptions\AccessGridException on transport-level failure
     */
    public function send(string $method, string $url, array $headers, ?string $body = null): HttpResponse;
}
