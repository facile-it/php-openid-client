<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Session;

use Facile\OpenIDClient\Session\AuthSession;
use Facile\OpenIDClientTest\TestCase;
use function json_decode;
use function json_encode;

/**
 * @internal
 * @coversNothing
 */
final class AuthSessionTest extends TestCase
{
    public function testFromArray(): void
    {
        $session = AuthSession::fromArray([
            'state' => 'foo',
            'nonce' => 'bar',
        ]);

        self::assertSame('foo', $session->getState());
        self::assertSame('bar', $session->getNonce());
    }

    public function testJsonSerializer(): void
    {
        $session = AuthSession::fromArray([
            'state' => 'foo',
            'nonce' => 'bar',
        ]);

        self::assertSame([
            'state' => 'foo',
            'nonce' => 'bar',
        ], json_decode(json_encode($session), true));
    }

    public function testSetCodeVerifier(): void
    {
        $session = new AuthSession();

        self::assertNull($session->getCodeVerifier());

        $session->setCodeVerifier('foo');

        self::assertSame('foo', $session->getCodeVerifier());
    }

    public function testSetCustoms(): void
    {
        $session = new AuthSession();

        self::assertSame([], $session->getCustoms());

        $session->setCustoms(['foo' => 'bar']);

        self::assertSame(['foo' => 'bar'], $session->getCustoms());
    }

    public function testSetNonce(): void
    {
        $session = new AuthSession();

        self::assertNull($session->getNonce());

        $session->setNonce('foo');

        self::assertSame('foo', $session->getNonce());
    }

    public function testSetState(): void
    {
        $session = new AuthSession();

        self::assertNull($session->getState());

        $session->setState('foo');

        self::assertSame('foo', $session->getState());
    }
}
