<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\AuthMethod;

use Facile\OpenIDClient\AuthMethod\ClientSecretPost;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class ClientSecretPostTest extends TestCase
{
    public function testGetSupportedMethod(): void
    {
        $auth = new ClientSecretPost();
        static::assertSame('client_secret_post', $auth->getSupportedMethod());
    }

    public function testCreateRequest(): void
    {
        $auth = new ClientSecretPost();

        $stream = $this->prophesize(StreamInterface::class);
        $request = $this->prophesize(RequestInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);

        $client->getMetadata()->willReturn($metadata->reveal());
        $metadata->getClientId()->willReturn('foo');
        $metadata->getClientSecret()->willReturn('bar');

        $stream->write('foo=bar&client_id=foo&client_secret=bar')
            ->shouldBeCalled();

        $request->getBody()->willReturn($stream);

        $result = $auth->createRequest(
            $request->reveal(),
            $client->reveal(),
            ['foo' => 'bar']
        );

        static::assertSame($request->reveal(), $result);
    }

    public function testCreateRequestWithNoClientSecret(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $auth = new ClientSecretPost();

        $request = $this->prophesize(RequestInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);

        $client->getMetadata()->willReturn($metadata->reveal());
        $metadata->getClientSecret()->willReturn(null);

        $auth->createRequest(
            $request->reveal(),
            $client->reveal(),
            []
        );
    }
}
