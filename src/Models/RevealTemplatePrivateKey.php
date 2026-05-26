<?php

namespace AccessGrid\Models;

/**
 * Result of a SmartTap private key reveal.
 *
 * `privateKey` is the plaintext PEM, decrypted client-side by the SDK.
 * The encrypted envelope is consumed internally and not exposed.
 */
class RevealTemplatePrivateKey
{
    public ?string $keyVersion;
    public ?string $collectorId;
    public ?string $fingerprint;
    public ?string $privateKey;

    // snake_case aliases
    public ?string $key_version;
    public ?string $collector_id;
    public ?string $private_key;

    public function __construct(array $data)
    {
        $this->keyVersion = $data['key_version'] ?? null;
        $this->collectorId = $data['collector_id'] ?? null;
        $this->fingerprint = $data['fingerprint'] ?? null;
        $this->privateKey = $data['private_key'] ?? null;

        $this->key_version = $this->keyVersion;
        $this->collector_id = $this->collectorId;
        $this->private_key = $this->privateKey;
    }
}
