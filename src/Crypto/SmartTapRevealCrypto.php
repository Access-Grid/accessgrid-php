<?php

namespace AccessGrid\Crypto;

/**
 * Internal crypto helpers for the SmartTap reveal flow.
 *
 * Driven by Console::revealSmartTap(); not part of the public API.
 *
 * @internal
 */
class SmartTapRevealCrypto
{
    private const CURVE = 'prime256v1';
    private const HKDF_INFO = 'accessgrid-smart-tap-reveal-v1';
    private const KEY_LEN = 32;

    /**
     * Generate a fresh P-256 keypair for a reveal call.
     *
     * @return array Shaped: ['priv' => OpenSSL key, 'pub_pem' => SubjectPublicKeyInfo PEM string].
     */
    public static function generateKeypair(): array
    {
        $priv = openssl_pkey_new([
            'curve_name' => self::CURVE,
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);
        if ($priv === false) {
            throw new \RuntimeException('Failed to generate EC keypair: ' . openssl_error_string());
        }

        $details = openssl_pkey_get_details($priv);

        return ['priv' => $priv, 'pub_pem' => $details['key']];
    }

    /**
     * Decrypt the encrypted_private_key envelope from the reveal endpoint.
     *
     * Performs ECDH(client_priv, server_ephemeral_pub) + HKDF-SHA256 + AES-256-GCM.
     * Must match the server-side encryption parameters exactly.
     *
     * @param array $envelope The encrypted_private_key map (alg/ephemeral_public_key/iv/ciphertext/tag).
     * @param mixed $privKey  The caller's private key (OpenSSL key resource or OpenSSLAsymmetricKey on PHP 8+).
     * @return string The plaintext SmartTap private key PEM.
     * @throws \RuntimeException on bad envelope or auth-tag verification failure.
     */
    public static function decryptEnvelope(array $envelope, $privKey): string
    {
        $serverPub = openssl_pkey_get_public($envelope['ephemeral_public_key'] ?? '');
        if ($serverPub === false) {
            throw new \RuntimeException('Invalid ephemeral_public_key in envelope');
        }

        // Natural-length shared secret (32 bytes / P-256 X coord). The third
        // arg was deprecated in PHP 8.4 as either ignored or truncating.
        $sharedSecret = openssl_pkey_derive($serverPub, $privKey);
        if ($sharedSecret === false) {
            throw new \RuntimeException('ECDH derivation failed: ' . openssl_error_string());
        }

        $aesKey = hash_hkdf('sha256', $sharedSecret, self::KEY_LEN, self::HKDF_INFO, '');

        $iv = base64_decode($envelope['iv'] ?? '', true);
        $ciphertext = base64_decode($envelope['ciphertext'] ?? '', true);
        $tag = base64_decode($envelope['tag'] ?? '', true);
        if ($iv === false || $ciphertext === false || $tag === false) {
            throw new \RuntimeException('Envelope iv/ciphertext/tag must be base64-encoded');
        }

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
            throw new \RuntimeException('AES-GCM decryption failed (auth tag verification)');
        }

        return $plaintext;
    }
}
