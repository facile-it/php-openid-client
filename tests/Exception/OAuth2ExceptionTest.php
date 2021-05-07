<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Exception;

use Facile\OpenIDClient\Exception\ExceptionInterface;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use Facile\OpenIDClient\Exception\OAuth2Exception;
use Facile\OpenIDClient\Exception\RemoteException;
use Facile\OpenIDClientTest\TestCase;
use function json_decode;
use function json_encode;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class OAuth2ExceptionTest extends TestCase
{
    public function testFromResponse(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $response->getBody()->willReturn($stream->reveal());
        $response->getReasonPhrase()->willReturn('Bad request');
        $response->getStatusCode()->willReturn(400);

        $stream->__toString()->willReturn('{"error": "error_code"}');

        $exception = OAuth2Exception::fromResponse($response->reveal());

        static::assertInstanceOf(ExceptionInterface::class, $exception);
        static::assertSame('error_code', $exception->getMessage());
        static::assertSame('error_code', $exception->getError());
        static::assertNull($exception->getDescription());
        static::assertNull($exception->getErrorUri());
    }

    public function testFromResponseComplete(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $response->getBody()->willReturn($stream->reveal());
        $response->getReasonPhrase()->willReturn('Bad request');
        $response->getStatusCode()->willReturn(400);

        $stream->__toString()->willReturn('{"error": "error_code","error_description":"Error message","error_uri":"uri"}');

        $exception = OAuth2Exception::fromResponse($response->reveal());

        static::assertInstanceOf(ExceptionInterface::class, $exception);
        static::assertSame('Error message (error_code)', $exception->getMessage());
        static::assertSame('error_code', $exception->getError());
        static::assertSame('Error message', $exception->getDescription());
        static::assertSame('uri', $exception->getErrorUri());
    }

    public function testFromResponseNoOAuthError(): void
    {
        $this->expectException(RemoteException::class);

        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $response->getBody()->willReturn($stream->reveal());
        $response->getReasonPhrase()->willReturn('Bad request');
        $response->getStatusCode()->willReturn(400);

        $stream->__toString()->willReturn('');

        OAuth2Exception::fromResponse($response->reveal());
    }

    public function testFromParameters(): void
    {
        $exception = OAuth2Exception::fromParameters([
            'error' => 'error_code',
            'error_description' => 'Error message',
            'error_uri' => 'uri',
        ]);

        static::assertInstanceOf(ExceptionInterface::class, $exception);
        static::assertSame('Error message (error_code)', $exception->getMessage());
        static::assertSame('error_code', $exception->getError());
        static::assertSame('Error message', $exception->getDescription());
        static::assertSame('uri', $exception->getErrorUri());
    }

    public function testFromInvalidParameters(): void
    {
        $this->expectException(InvalidArgumentException::class);

        OAuth2Exception::fromParameters([
            'error_description' => 'Error message',
            'error_uri' => 'uri',
        ]);
    }

    public function testJsonSerializer(): void
    {
        $params = [
            'error' => 'error_code',
            'error_description' => 'Error message',
            'error_uri' => 'uri',
        ];
        $exception = OAuth2Exception::fromParameters($params);

        static::assertInstanceOf(ExceptionInterface::class, $exception);
        static::assertSame($params, json_decode(json_encode($exception), true));
    }
}
