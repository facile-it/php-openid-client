<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Issuer\Metadata\Provider;

use Facile\OpenIDClient\Issuer\Metadata\Provider\DiscoveryProviderInterface;
use Facile\OpenIDClient\Issuer\Metadata\Provider\WebFingerProvider;
use Facile\OpenIDClientTest\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use function http_build_query;
use function json_encode;

/**
 * @internal
 * @coversNothing
 */
final class WebFingerProviderTest extends TestCase
{
    public function testFetch(): void
    {
        $client = $this->prophesize(ClientInterface::class);
        $requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $uriFactory = $this->prophesize(UriFactoryInterface::class);
        $discoveryProvider = $this->prophesize(DiscoveryProviderInterface::class);

        $resource = 'joe@example.com';
        $provider = new WebFingerProvider(
            $client->reveal(),
            $requestFactory->reveal(),
            $uriFactory->reveal(),
            $discoveryProvider->reveal()
        );

        $webFingerUrl = $this->prophesize(UriInterface::class);
        $webFingerUrl1 = $this->prophesize(UriInterface::class);

        $uriFactory->createUri('https://example.com/.well-known/webfinger')
            ->willReturn($webFingerUrl1->reveal());
        $webFingerUrl1->withQuery(http_build_query([
            'resource' => 'acct:joe@example.com',
            'rel' => 'http://openid.net/specs/connect/1.0/issuer',
        ]))
            ->willReturn($webFingerUrl->reveal());
        $webFingerUrl->__toString()->willReturn('https://example.com/.well-known/webfinger');

        $request = $this->prophesize(RequestInterface::class);
        $request1 = $this->prophesize(RequestInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $requestFactory->createRequest('GET', $webFingerUrl)
            ->willReturn($request1->reveal());
        $request1->withHeader('accept', 'application/json')
            ->willReturn($request->reveal());

        $client->sendRequest($request->reveal())
            ->willReturn($response->reveal());

        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($stream->reveal());

        $responsePayload = [
            'links' => [
                [
                    'rel' => 'wrong-foo',
                    'href' => 'wrong-url',
                ],
                [
                    'rel' => 'http://openid.net/specs/connect/1.0/issuer',
                ],
                [
                    'rel' => 'http://openid.net/specs/connect/1.0/issuer',
                    'href' => 'https://openid-uri',
                ],
            ],
        ];

        $stream->__toString()->willReturn(json_encode($responsePayload));

        $discoveryProvider->discovery('https://openid-uri')->willReturn([
            'issuer' => 'https://openid-uri',
        ]);

        self::assertSame(['issuer' => 'https://openid-uri'], $provider->fetch($resource));
    }
}
