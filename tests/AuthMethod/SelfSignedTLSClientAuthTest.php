<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\AuthMethod;

use Facile\OpenIDClient\AuthMethod\SelfSignedTLSClientAuth;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClientTest\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @internal
 * @coversNothing
 */
final class SelfSignedTLSClientAuthTest extends TestCase
{
    public function testCreateRequest(): void
    {
        $auth = new SelfSignedTLSClientAuth();

        $stream = $this->prophesize(StreamInterface::class);
        $request = $this->prophesize(RequestInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);

        $client->getMetadata()->willReturn($metadata->reveal());
        $metadata->getClientId()->willReturn('foo');
        $metadata->getClientSecret()->shouldNotBeCalled();

        $stream->write('foo=bar&client_id=foo')->shouldBeCalled();

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
        $auth = new SelfSignedTLSClientAuth();
        self::assertSame('self_signed_tls_client_auth', $auth->getSupportedMethod());
    }
}
