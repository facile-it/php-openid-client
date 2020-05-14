<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\AuthMethod;

use Facile\OpenIDClient\AuthMethod\None;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class NoneTest extends TestCase
{
    public function testGetSupportedMethod(): void
    {
        $auth = new None();
        static::assertSame('none', $auth->getSupportedMethod());
    }

    public function testCreateRequest(): void
    {
        $auth = new None();

        $stream = $this->prophesize(StreamInterface::class);
        $request = $this->prophesize(RequestInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $clientMetadata = new ClientMetadata('clientId');

        $client->getMetadata()->willReturn($clientMetadata);

        $stream->write('client_id=clientId&foo=bar')->shouldBeCalled();

        $request->getBody()->willReturn($stream->reveal());

        $result = $auth->createRequest(
            $request->reveal(),
            $client->reveal(),
            ['foo' => 'bar']
        );

        static::assertSame($request->reveal(), $result);
    }
}
