<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Claims;

use Facile\OpenIDClient\Claims\DistributedParser;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Issuer\IssuerBuilderInterface;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use Facile\OpenIDClientTest\TestCase;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Signature\JWSVerifier;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use function Facile\OpenIDClient\base64url_encode;
use function implode;
use function json_encode;

/**
 * @internal
 * @coversNothing
 */
final class DistributedClaimsTest extends TestCase
{
    public function testUnpackAggregatedClaims(): void
    {
        $jwt = implode('.', [
            base64url_encode((string) json_encode(['alg' => 'none'])),
            base64url_encode((string) json_encode(['age' => 30])),
            '.',
        ]);

        $algorithmManager = $this->prophesize(AlgorithmManager::class);
        $JWSVerifier = $this->prophesize(JWSVerifier::class);
        $issuerBuilder = $this->prophesize(IssuerBuilderInterface::class);
        $httpClient = $this->prophesize(HttpClient::class);
        $requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $issuer = $this->prophesize(IssuerInterface::class);
        $request = $this->prophesize(RequestInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $requestFactory->createRequest('GET', 'https://endpoint.url/claims')
            ->willReturn($request->reveal());

        $request->withHeader('accept', 'application/jwt')
            ->willReturn($request->reveal());
        $request->withHeader('authorization', 'Bearer ' . 'access-token')
            ->willReturn($request->reveal());

        $response->getStatusCode()->willReturn(201);
        $response->getBody()->willReturn($stream->reveal());
        $stream->__toString()->willReturn($jwt);

        $httpClient->sendRequest($request->reveal())
            ->willReturn($response->reveal());

        $client->getIssuer()->willReturn($issuer->reveal());

        $service = new DistributedParser(
            $issuerBuilder->reveal(),
            $httpClient->reveal(),
            $requestFactory->reveal(),
            $algorithmManager->reveal(),
            $JWSVerifier->reveal()
        );

        $claims = [
            'sub' => 'foo',
            '_claim_names' => [
                'age' => 'src1',
            ],
            '_claim_sources' => [
                'src1' => [
                    'endpoint' => 'https://endpoint.url/claims',
                    'access_token' => 'access-token',
                ],
            ],
        ];

        $unpacked = $service->fetch($client->reveal(), $claims);

        self::assertSame(30, $unpacked['age'] ?? null);
        self::assertArrayNotHasKey('_claim_names', $unpacked);
        self::assertArrayNotHasKey('_claim_sources', $unpacked);
    }

    public function testUnpackAggregatedClaimsWithNoClaimNames(): void
    {
        $algorithmManager = $this->prophesize(AlgorithmManager::class);
        $JWSVerifier = $this->prophesize(JWSVerifier::class);
        $issuerBuilder = $this->prophesize(IssuerBuilderInterface::class);
        $httpClient = $this->prophesize(HttpClient::class);
        $requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $client = $this->prophesize(ClientInterface::class);

        $service = new DistributedParser(
            $issuerBuilder->reveal(),
            $httpClient->reveal(),
            $requestFactory->reveal(),
            $algorithmManager->reveal(),
            $JWSVerifier->reveal()
        );

        $claims = [
            'sub' => 'foo',
            '_claim_sources' => [
                'src1' => [
                    'endpoint' => 'https://endpoint.url/claims',
                    'access_token' => 'access-token',
                ],
            ],
        ];

        $distributed = $service->fetch($client->reveal(), $claims);

        self::assertSame($claims, $distributed);
    }

    public function testUnpackAggregatedClaimsWithNoClaimSources(): void
    {
        $algorithmManager = $this->prophesize(AlgorithmManager::class);
        $JWSVerifier = $this->prophesize(JWSVerifier::class);
        $issuerBuilder = $this->prophesize(IssuerBuilderInterface::class);
        $httpClient = $this->prophesize(HttpClient::class);
        $requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $client = $this->prophesize(ClientInterface::class);

        $service = new DistributedParser(
            $issuerBuilder->reveal(),
            $httpClient->reveal(),
            $requestFactory->reveal(),
            $algorithmManager->reveal(),
            $JWSVerifier->reveal()
        );

        $claims = [
            'sub' => 'foo',
            '_claim_names' => [
                'age' => 'src1',
            ],
        ];

        $distributed = $service->fetch($client->reveal(), $claims);

        self::assertSame($claims, $distributed);
    }

    public function testUnpackAggregatedClaimsWithResourceError(): void
    {
        $jwt = implode('.', [
            base64url_encode((string) json_encode(['alg' => 'none'])),
            base64url_encode((string) json_encode(['age' => 30])),
            '.',
        ]);

        $algorithmManager = $this->prophesize(AlgorithmManager::class);
        $JWSVerifier = $this->prophesize(JWSVerifier::class);
        $issuerBuilder = $this->prophesize(IssuerBuilderInterface::class);
        $httpClient = $this->prophesize(HttpClient::class);
        $requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $issuer = $this->prophesize(IssuerInterface::class);
        $request = $this->prophesize(RequestInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);

        $requestFactory->createRequest('GET', 'https://endpoint.url/claims')
            ->willReturn($request->reveal());

        $request->withHeader('accept', 'application/jwt')
            ->willReturn($request->reveal());
        $request->withHeader('authorization', 'Bearer ' . 'access-token')
            ->willReturn($request->reveal());

        $response->getReasonPhrase()->willReturn('foo');
        $response->getStatusCode()->willReturn(401);
        $response->getBody()->willReturn($stream->reveal());
        $stream->__toString()->willReturn($jwt);

        $httpClient->sendRequest($request->reveal())
            ->willReturn($response->reveal());

        $client->getIssuer()->willReturn($issuer->reveal());

        $service = new DistributedParser(
            $issuerBuilder->reveal(),
            $httpClient->reveal(),
            $requestFactory->reveal(),
            $algorithmManager->reveal(),
            $JWSVerifier->reveal()
        );

        $claims = [
            'sub' => 'foo',
            '_claim_names' => [
                'age' => 'src1',
            ],
            '_claim_sources' => [
                'src1' => [
                    'endpoint' => 'https://endpoint.url/claims',
                    'access_token' => 'access-token',
                ],
            ],
        ];

        $unpacked = $service->fetch($client->reveal(), $claims);

        self::assertSame($claims, $unpacked);
    }
}
