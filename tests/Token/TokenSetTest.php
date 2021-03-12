<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Token;

use Facile\OpenIDClient\Token\TokenSet;
use Facile\OpenIDClientTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class TokenSetTest extends TestCase
{
    public function testGetAccessToken(): void
    {
        $authResponse = TokenSet::fromParams([]);
        self::assertNull($authResponse->getAccessToken());

        $authResponse = TokenSet::fromParams(['access_token' => 'foo']);
        self::assertSame('foo', $authResponse->getAccessToken());
    }

    public function testGetCode(): void
    {
        $authResponse = TokenSet::fromParams([]);
        self::assertNull($authResponse->getCode());

        $authResponse = TokenSet::fromParams(['code' => 'foo']);
        self::assertSame('foo', $authResponse->getCode());
    }

    public function testGetCodeVerifier(): void
    {
        $authResponse = TokenSet::fromParams([]);
        self::assertNull($authResponse->getCodeVerifier());

        $authResponse = TokenSet::fromParams(['code_verifier' => 'foo']);
        self::assertSame('foo', $authResponse->getCodeVerifier());
    }

    public function testGetExpiresIn(): void
    {
        $authResponse = TokenSet::fromParams([]);
        self::assertNull($authResponse->getExpiresIn());

        $authResponse = TokenSet::fromParams(['expires_in' => '3']);
        self::assertSame(3, $authResponse->getExpiresIn());
    }

    public function testGetIdToken(): void
    {
        $authResponse = TokenSet::fromParams([]);
        self::assertNull($authResponse->getIdToken());

        $authResponse = TokenSet::fromParams(['id_token' => 'foo']);
        self::assertSame('foo', $authResponse->getIdToken());
    }

    public function testGetRefreshToken(): void
    {
        $authResponse = TokenSet::fromParams([]);
        self::assertNull($authResponse->getRefreshToken());

        $authResponse = TokenSet::fromParams(['refresh_token' => 'foo']);
        self::assertSame('foo', $authResponse->getRefreshToken());
    }

    public function testGetState(): void
    {
        $authResponse = TokenSet::fromParams([]);
        self::assertNull($authResponse->getState());

        $authResponse = TokenSet::fromParams(['state' => 'foo']);
        self::assertSame('foo', $authResponse->getState());
    }

    public function testGetTokenType(): void
    {
        $authResponse = TokenSet::fromParams([]);
        self::assertNull($authResponse->getTokenType());

        $authResponse = TokenSet::fromParams(['token_type' => 'foo']);
        self::assertSame('foo', $authResponse->getTokenType());
    }
}
