<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Exception;

use Facile\OpenIDClient\Exception\ExceptionInterface;
use Facile\OpenIDClient\Exception\RemoteException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class RemoteExceptionTest extends TestCase
{
    public function testException(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getReasonPhrase()->willReturn('Error message');
        $response->getStatusCode()->willReturn(400);

        $exception = new RemoteException($response->reveal());

        static::assertInstanceOf(ExceptionInterface::class, $exception);
        static::assertSame('Error message', $exception->getMessage());
        static::assertSame(400, $exception->getCode());
        static::assertSame($response->reveal(), $exception->getResponse());
    }

    public function testExceptionWithCustomMessage(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getReasonPhrase()->willReturn('Error message');
        $response->getStatusCode()->willReturn(400);

        $exception = new RemoteException($response->reveal(), 'foo');

        static::assertInstanceOf(ExceptionInterface::class, $exception);
        static::assertSame('foo', $exception->getMessage());
        static::assertSame(400, $exception->getCode());
        static::assertSame($response->reveal(), $exception->getResponse());
    }
}
