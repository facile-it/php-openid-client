<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Issuer\Metadata\Provider;

use Facile\OpenIDClient\Issuer\Metadata\Provider\DiscoveryProvider;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class DiscoveryProviderTest extends TestCase
{
    /** @var ClientInterface|ObjectProphecy */
    private $client;

    /** @var RequestFactoryInterface|ObjectProphecy */
    private $requestFactory;

    /** @var UriFactoryInterface|ObjectProphecy */
    private $uriFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->prophesize(ClientInterface::class);
        $this->requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $this->uriFactory = $this->prophesize(UriFactoryInterface::class);
    }

    /**
     * @param string $uri
     */
    private function prepareForDiscovery(string $uri): void
    {
        $client = $this->client;
        $requestFactory = $this->requestFactory;
        $uriFactory = $this->uriFactory;

        $request1 = $this->prophesize(RequestInterface::class);
        $request2 = $this->prophesize(RequestInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $response->getBody()->willReturn($stream->reveal());
        $response->getStatusCode()->willReturn(200);
        $stream->__toString()->willReturn('{"issuer":"https://openid-uri"}');

        $request1->withHeader('accept', 'application/json')
            ->shouldBeCalled()
            ->willReturn($request2->reveal());

        $uri1 = $this->prophesize(UriInterface::class);

        $uri1->getPath()->willReturn('/.well-known/openid-configuration');

        $uri1->__toString()->willReturn('https://example.com/.well-known/openid-configuration');

        $requestFactory->createRequest('GET', 'https://example.com/.well-known/openid-configuration')
            ->willReturn($request1->reveal());

        $client->sendRequest($request2->reveal())
            ->willReturn($response->reveal());

        $uriFactory->createUri($uri)->willReturn($uri1->reveal());
    }

    public function testDiscovery(): void
    {
        $client = $this->client;
        $requestFactory = $this->requestFactory;
        $uriFactory = $this->uriFactory;

        $uri = 'https://example.com';
        $provider = new DiscoveryProvider(
            $client->reveal(),
            $requestFactory->reveal(),
            $uriFactory->reveal()
        );

        $uri2 = $this->prophesize(UriInterface::class);
        $uri2->__toString()->willReturn('https://example.com');
        $uri2->getPath()->willReturn('/');

        $uriFactory->createUri('https://example.com')
            ->willReturn($uri2->reveal());

        $uriOpenid = $this->prophesize(UriInterface::class);
        $uriOpenid->__toString()->willReturn('https://example.com/.well-known/openid-configuration');
        $uri2->withPath('/.well-known/openid-configuration')
            ->willReturn($uriOpenid->reveal());

        $uriOAuth = $this->prophesize(UriInterface::class);
        $uriOAuth->__toString()->willReturn('https://example.com/.well-known/openid-configuration');
        $uri2->withPath('/.well-known/oauth-authorization-server')
            ->willReturn($uriOAuth->reveal());

        $this->prepareForDiscovery('https://example.com/.well-known/openid-configuration');

        static::assertSame(['issuer' => 'https://openid-uri'], $provider->discovery($uri));
    }

    public function testDiscoveryWithWellKnown(): void
    {
        $client = $this->client;
        $requestFactory = $this->requestFactory;
        $uriFactory = $this->uriFactory;

        $uri = 'https://example.com/.well-known/openid-configuration';
        $provider = new DiscoveryProvider(
            $client->reveal(),
            $requestFactory->reveal(),
            $uriFactory->reveal()
        );

        $this->prepareForDiscovery('https://example.com/.well-known/openid-configuration');

        static::assertSame(['issuer' => 'https://openid-uri'], $provider->discovery($uri));
    }
}
