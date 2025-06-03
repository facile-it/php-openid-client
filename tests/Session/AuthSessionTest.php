<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Session;

use Facile\OpenIDClient\Session\AuthSession;
use Facile\OpenIDClientTest\TestCase;

use function json_decode;
use function json_encode;

class AuthSessionTest extends TestCase
{
    public function testSetState(): void
    {
        $session = new AuthSession();

        static::assertNull($session->getState());

        $session->setState('foo');

        static::assertSame('foo', $session->getState());
    }

    public function testSetNonce(): void
    {
        $session = new AuthSession();

        static::assertNull($session->getNonce());

        $session->setNonce('foo');

        static::assertSame('foo', $session->getNonce());
    }

    public function testSetCodeVerifier(): void
    {
        $session = new AuthSession();

        static::assertNull($session->getCodeVerifier());

        $session->setCodeVerifier('foo');

        static::assertSame('foo', $session->getCodeVerifier());
    }

    public function testSetCustoms(): void
    {
        $session = new AuthSession();

        static::assertSame([], $session->getCustoms());

        $session->setCustoms(['foo' => 'bar']);

        static::assertSame(['foo' => 'bar'], $session->getCustoms());
    }

    public function testFromArray(): void
    {
        $session = AuthSession::fromArray([
            'state' => 'foo',
            'nonce' => 'bar',
        ]);

        static::assertSame('foo', $session->getState());
        static::assertSame('bar', $session->getNonce());
    }

    public function testJsonSerializer(): void
    {
        $session = AuthSession::fromArray([
            'state' => 'foo',
            'nonce' => 'bar',
        ]);

        static::assertSame([
            'state' => 'foo',
            'nonce' => 'bar',
        ], json_decode(json_encode($session), true));
    }
}
