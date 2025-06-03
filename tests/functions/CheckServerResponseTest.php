<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\functions;

use Facile\OpenIDClient\Exception\OAuth2Exception;
use Facile\OpenIDClient\Exception\RemoteException;
use Facile\OpenIDClientTest\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use function Facile\OpenIDClient\check_server_response;

class CheckServerResponseTest extends TestCase
{
    public function testErrorStatusCode(): void
    {
        $this->expectException(RemoteException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Error');

        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $stream->__toString()->willReturn('');
        $response->getBody()->willReturn($stream->reveal());
        $response->getStatusCode()->willReturn(400);
        $response->getReasonPhrase()->willReturn('Error');

        check_server_response($response->reveal());
    }

    public function testErrorStatusCodeWithOAuth2Error(): void
    {
        $this->expectException(OAuth2Exception::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('foo');

        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $stream->__toString()->willReturn('{"error":"foo"}');
        $response->getBody()->willReturn($stream->reveal());
        $response->getStatusCode()->willReturn(400);
        $response->getReasonPhrase()->shouldNotBeCalled();

        check_server_response($response->reveal());
    }

    public function testErrorStatusCodeWithOAuth2ErrorAndExpectedCode(): void
    {
        $this->expectException(OAuth2Exception::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage('foo');

        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $stream->__toString()->willReturn('{"error":"foo"}');
        $response->getBody()->willReturn($stream->reveal());
        $response->getStatusCode()->willReturn(400);
        $response->getReasonPhrase()->shouldNotBeCalled();

        check_server_response($response->reveal(), 200);
    }

    public function testErrorStatusCodeWithExpectedCode(): void
    {
        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $stream->__toString()->willReturn('{"error":"foo"}');
        $response->getBody()->willReturn($stream->reveal());
        $response->getStatusCode()->shouldBeCalled()->willReturn(400);

        check_server_response($response->reveal(), 400);
    }

    public function testErrorStatusCodeWithRemoteException(): void
    {
        $this->expectException(RemoteException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Bad request');

        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $stream->__toString()->willReturn('error string');
        $response->getBody()->willReturn($stream->reveal());
        $response->getStatusCode()->willReturn(400);
        $response->getReasonPhrase()->willReturn('Bad request');

        check_server_response($response->reveal());
    }
}
