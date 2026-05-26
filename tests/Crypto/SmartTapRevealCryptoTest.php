<?php

namespace AccessGrid\Tests\Crypto;

use PHPUnit\Framework\TestCase;
use AccessGrid\Crypto\SmartTapRevealCrypto;

class SmartTapRevealCryptoTest extends TestCase
{
    // Captured test vector: a real envelope produced by the server against a
    // sentinel `smart_tap_key` value. Lets us verify the SDK's decrypt is
    // wire-compatible without reproducing the server's encrypt in test code.
    //
    // The caller_private_key is ephemeral and single-use by design (the server
    // rejects reuse on pubkey fingerprint), so committing it carries no
    // credential risk.
    private const FIXTURE_CALLER_PRIVATE_KEY_PEM = <<<PEM
        -----BEGIN EC PRIVATE KEY-----
        MHcCAQEEIIou+Kk08kWAjhi0WyIx+L2GrgStGBCPODlwKYKd5BydoAoGCCqGSM49
        AwEHoUQDQgAE+gnDxXJt1SBaCK8roKH8QvOa/ItdQUe85JIsUc6RvhD/udLaFtHY
        m+MnOmeSdVaKTPWudH0+iGbleB3kS7lYxQ==
        -----END EC PRIVATE KEY-----
        PEM;

    private const FIXTURE_EXPECTED_PLAINTEXT = 'FIXTURE-PLAINTEXT-NOT-A-CREDENTIAL';

    private static function fixtureEnvelope(): array
    {
        return [
            'alg' => 'ECDH-ES+A256GCM',
            'ciphertext' => 'ckYyA3FdRYjOFI/FKz/QeR5Yf9nZZFzo73kDXKZSB/EgbQ==',
            'ephemeral_public_key' => "-----BEGIN PUBLIC KEY-----\nMFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAE7mg6i99GcIVutMPr/PXSBSQVlbLM\ntnJO10ZBjk9ZTfw6wwAVNBnDBiqY7VrdOG1JdFOYoac+NkAlyMRGYk2tVQ==\n-----END PUBLIC KEY-----\n",
            'iv' => '5X2OCht+kLB/xQmX',
            'tag' => '0vwkjVaCwi5zl37xvJPxeg==',
        ];
    }

    private static function fixtureCallerPrivateKey()
    {
        // Heredoc preserved leading indentation; strip it before openssl reads.
        $pem = preg_replace('/^ +/m', '', self::FIXTURE_CALLER_PRIVATE_KEY_PEM);
        return openssl_pkey_get_private($pem);
    }

    public function testDecryptsCapturedServerEnvelope(): void
    {
        $plaintext = SmartTapRevealCrypto::decryptEnvelope(
            self::fixtureEnvelope(),
            self::fixtureCallerPrivateKey()
        );

        $this->assertSame(self::FIXTURE_EXPECTED_PLAINTEXT, $plaintext);
    }

    public function testTamperedTagFailsDecryption(): void
    {
        $envelope = self::fixtureEnvelope();
        $tag = base64_decode($envelope['tag']);
        $tag[0] = chr(ord($tag[0]) ^ 0x01);
        $envelope['tag'] = base64_encode($tag);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('AES-GCM decryption failed');

        SmartTapRevealCrypto::decryptEnvelope($envelope, self::fixtureCallerPrivateKey());
    }

    public function testWrongPrivateKeyFailsDecryption(): void
    {
        $wrong = openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('AES-GCM decryption failed');

        SmartTapRevealCrypto::decryptEnvelope(self::fixtureEnvelope(), $wrong);
    }

    public function testMissingEphemeralPublicKeyThrows(): void
    {
        $envelope = self::fixtureEnvelope();
        unset($envelope['ephemeral_public_key']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid ephemeral_public_key');

        SmartTapRevealCrypto::decryptEnvelope($envelope, self::fixtureCallerPrivateKey());
    }

    public function testNonBase64IvThrows(): void
    {
        $envelope = self::fixtureEnvelope();
        $envelope['iv'] = 'not!base64!';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('base64');

        SmartTapRevealCrypto::decryptEnvelope($envelope, self::fixtureCallerPrivateKey());
    }

    public function testGenerateKeypairProducesUsablePemAndKey(): void
    {
        $keypair = SmartTapRevealCrypto::generateKeypair();

        $this->assertArrayHasKey('priv', $keypair);
        $this->assertArrayHasKey('pub_pem', $keypair);
        $this->assertStringContainsString('-----BEGIN PUBLIC KEY-----', $keypair['pub_pem']);
    }

    public function testGenerateKeypairReturnsDistinctKeys(): void
    {
        $a = SmartTapRevealCrypto::generateKeypair();
        $b = SmartTapRevealCrypto::generateKeypair();

        $this->assertNotSame($a['pub_pem'], $b['pub_pem']);
    }
}
