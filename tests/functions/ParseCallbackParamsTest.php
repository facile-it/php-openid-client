<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\functions;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Facile\OpenIDClient\Exception\RuntimeException;
use function Facile\OpenIDClient\parse_callback_params;

class ParseCallbackParamsTest extends TestCase
{
    public function testWithPost(): void
    {
        $urlEncoded = 'foo=bar&foo2=bar2';
        $expected = ['foo' => 'bar', 'foo2' => 'bar2'];

        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $serverRequest->getMethod()->willReturn('POST');
        $serverRequest->getBody()->willReturn($stream->reveal());
        $stream->__toString()->willReturn($urlEncoded);

        $params = parse_callback_params($serverRequest->reveal());

        static::assertSame($expected, $params);
    }

    public function testWithGetFragment(): void
    {
        $urlEncoded = 'foo=bar&foo2=bar2';
        $expected = ['foo' => 'bar', 'foo2' => 'bar2'];

        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $uri = $this->prophesize(UriInterface::class);

        $serverRequest->getMethod()->willReturn('GET');
        $serverRequest->getUri()->willReturn($uri->reveal());
        $uri->getFragment()->willReturn($urlEncoded);

        $params = parse_callback_params($serverRequest->reveal());

        static::assertSame($expected, $params);
    }

    public function testWithGetQuery(): void
    {
        $urlEncoded = 'foo=bar&foo2=bar2';
        $expected = ['foo' => 'bar', 'foo2' => 'bar2'];

        $serverRequest = $this->prophesize(ServerRequestInterface::class);
        $uri = $this->prophesize(UriInterface::class);

        $serverRequest->getMethod()->willReturn('GET');
        $serverRequest->getUri()->willReturn($uri->reveal());
        $uri->getFragment()->willReturn('');
        $uri->getQuery()->willReturn($urlEncoded);

        $params = parse_callback_params($serverRequest->reveal());

        static::assertSame($expected, $params);
    }

    public function testWithInvalidMethod(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid callback method');

        $serverRequest = $this->prophesize(ServerRequestInterface::class);

        $serverRequest->getMethod()->willReturn('PUT');
        parse_callback_params($serverRequest->reveal());
    }
}
