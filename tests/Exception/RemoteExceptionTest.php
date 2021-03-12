<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Exception;

use Facile\OpenIDClient\Exception\ExceptionInterface;
use Facile\OpenIDClient\Exception\RemoteException;
use Facile\OpenIDClientTest\TestCase;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 * @coversNothing
 */
final class RemoteExceptionTest extends TestCase
{
    public function testException(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getReasonPhrase()->willReturn('Error message');
        $response->getStatusCode()->willReturn(400);

        $exception = new RemoteException($response->reveal());

        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertSame('Error message', $exception->getMessage());
        self::assertSame(400, $exception->getCode());
        self::assertSame($response->reveal(), $exception->getResponse());
    }

    public function testExceptionWithCustomMessage(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getReasonPhrase()->willReturn('Error message');
        $response->getStatusCode()->willReturn(400);

        $exception = new RemoteException($response->reveal(), 'foo');

        self::assertInstanceOf(ExceptionInterface::class, $exception);
        self::assertSame('foo', $exception->getMessage());
        self::assertSame(400, $exception->getCode());
        self::assertSame($response->reveal(), $exception->getResponse());
    }
}
