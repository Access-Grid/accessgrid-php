<?php

namespace AccessGrid\Services;

use AccessGrid\Exceptions\AccessGridException;

/**
 * Client-side crypto helpers for the SmartTap reveal endpoint.
 *
 * The server returns the template's private key encrypted under
 * ECDH-ES + HKDF-SHA256 + AES-256-GCM. This class generates the local P-256
 * keypair, exposes the public key as PEM, and decrypts the server's envelope
 * so the plaintext private key is reconstructed without leaving this process.
 *
 * @internal
 */
final class SmartTapRevealCrypto
{
    private const CURVE = 'prime256v1';
    private const HKDF_INFO = 'accessgrid-smart-tap-reveal-v1';
    private const AES_KEY_LENGTH = 32;

    /**
     * @return array{key: \OpenSSLAsymmetricKey, public_key_pem: string}
     */
    public static function generateKeyPair(): array
    {
        $key = openssl_pkey_new([
            'curve_name' => self::CURVE,
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);

        if ($key === false) {
            throw new AccessGridException('Failed to generate P-256 keypair: ' . openssl_error_string());
        }

        $details = openssl_pkey_get_details($key);
        if ($details === false || empty($details['key'])) {
            throw new AccessGridException('Failed to extract public key PEM');
        }

        return [
            'key' => $key,
            'public_key_pem' => $details['key'],
        ];
    }

    /**
     * Decrypt the server's envelope using the locally-generated keypair.
     *
     * @param \OpenSSLAsymmetricKey $localKey
     * @param string                $serverEphemeralPublicKeyPem
     * @param string                $iv          Raw IV bytes (12 bytes)
     * @param string                $ciphertext  Raw ciphertext bytes
     * @param string                $tag         Raw GCM tag (16 bytes)
     * @return string Decrypted plaintext (PEM-encoded private key)
     */
    public static function decryptEnvelope(
        $localKey,
        string $serverEphemeralPublicKeyPem,
        string $iv,
        string $ciphertext,
        string $tag
    ): string {
        $serverPub = openssl_pkey_get_public($serverEphemeralPublicKeyPem);
        if ($serverPub === false) {
            throw new AccessGridException('Failed to parse server ephemeral_public_key PEM');
        }

        // ECDH: raw shared secret (X coordinate, 32 bytes for P-256).
        // Length param is implicit; PHP 8.5 deprecated it.
        $sharedSecret = openssl_pkey_derive($serverPub, $localKey);
        if ($sharedSecret === false) {
            throw new AccessGridException('ECDH key derivation failed: ' . openssl_error_string());
        }

        // HKDF-SHA256(shared, salt="", info="accessgrid-smart-tap-reveal-v1") -> 32-byte AES key.
        $aesKey = hash_hkdf('sha256', $sharedSecret, self::AES_KEY_LENGTH, self::HKDF_INFO, '');

        $plaintext = openssl_decrypt(
            $ciphertext,
            'aes-256-gcm',
            $aesKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            ''
        );

        if ($plaintext === false) {
            throw new AccessGridException('Failed to decrypt SmartTap envelope: ' . openssl_error_string());
        }

        return $plaintext;
    }
}
