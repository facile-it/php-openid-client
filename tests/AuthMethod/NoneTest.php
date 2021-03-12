<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\AuthMethod;

use Facile\OpenIDClient\AuthMethod\None;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClientTest\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 * @coversNothing
 */
final class NoneTest extends TestCase
{
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

        self::assertSame($request->reveal(), $result);
    }

    public function testGetSupportedMethod(): void
    {
        $auth = new None();
        self::assertSame('none', $auth->getSupportedMethod());
    }
}
