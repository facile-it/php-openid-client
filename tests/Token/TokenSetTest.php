<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Token;

use PHPUnit\Framework\TestCase;
use Facile\OpenIDClient\Token\TokenSet;

class TokenSetTest extends TestCase
{
    public function testGetTokenType(): void
    {
        $authResponse = new TokenSet();
        static::assertNull($authResponse->getTokenType());

        $authResponse = TokenSet::fromParams(['token_type' => 'foo']);
        static::assertSame('foo', $authResponse->getTokenType());
    }

    public function testGetCode(): void
    {
        $authResponse = new TokenSet();
        static::assertNull($authResponse->getCode());

        $authResponse = TokenSet::fromParams(['code' => 'foo']);
        static::assertSame('foo', $authResponse->getCode());
    }

    public function testGetExpiresIn(): void
    {
        $authResponse = new TokenSet();
        static::assertNull($authResponse->getExpiresIn());

        $authResponse = TokenSet::fromParams(['expires_in' => '3']);
        static::assertSame(3, $authResponse->getExpiresIn());
    }

    public function testGetIdToken(): void
    {
        $authResponse = new TokenSet();
        static::assertNull($authResponse->getIdToken());

        $authResponse = TokenSet::fromParams(['id_token' => 'foo']);
        static::assertSame('foo', $authResponse->getIdToken());
    }

    public function testGetRefreshToken(): void
    {
        $authResponse = new TokenSet();
        static::assertNull($authResponse->getRefreshToken());

        $authResponse = TokenSet::fromParams(['refresh_token' => 'foo']);
        static::assertSame('foo', $authResponse->getRefreshToken());
    }

    public function testGetCodeVerifier(): void
    {
        $authResponse = new TokenSet();
        static::assertNull($authResponse->getCodeVerifier());

        $authResponse = TokenSet::fromParams(['code_verifier' => 'foo']);
        static::assertSame('foo', $authResponse->getCodeVerifier());
    }

    public function testGetState(): void
    {
        $authResponse = new TokenSet();
        static::assertNull($authResponse->getState());

        $authResponse = TokenSet::fromParams(['state' => 'foo']);
        static::assertSame('foo', $authResponse->getState());
    }

    public function testGetAccessToken(): void
    {
        $authResponse = new TokenSet();
        static::assertNull($authResponse->getAccessToken());

        $authResponse = TokenSet::fromParams(['access_token' => 'foo']);
        static::assertSame('foo', $authResponse->getAccessToken());
    }
}
