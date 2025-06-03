<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Claims;

use Facile\JoseVerifier\JWK\JwksProviderInterface;
use Facile\OpenIDClient\Claims\AggregateParser;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Issuer\IssuerBuilderInterface;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadataInterface;
use Facile\OpenIDClientTest\TestCase;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializer;
use Prophecy\Argument;

use function json_encode;

class AggregatedClaimsTest extends TestCase
{
    public function testUnpackAggregatedClaimsWithNoClaimSources(): void
    {
        $algorithmManager = $this->prophesize(AlgorithmManager::class);
        $JWSVerifier = $this->prophesize(JWSVerifier::class);
        $JWSSerializer = $this->prophesize(JWSSerializer::class);
        $client = $this->prophesize(ClientInterface::class);
        $issuerBuilder = $this->prophesize(IssuerBuilderInterface::class);

        $service = new AggregateParser(
            $issuerBuilder->reveal(),
            $algorithmManager->reveal(),
            $JWSVerifier->reveal(),
            $JWSSerializer->reveal()
        );

        $claims = [
            'sub' => 'foo',
            '_claim_names' => [
                'eye_color' => 'src1',
                'shoe_size' => 'src1',
            ],
        ];

        $unpacked = $service->unpack($client->reveal(), $claims);

        static::assertSame($claims, $unpacked);
    }

    public function testUnpackAggregatedClaimsWithNoClaimNames(): void
    {
        $algorithmManager = $this->prophesize(AlgorithmManager::class);
        $JWSVerifier = $this->prophesize(JWSVerifier::class);
        $issuerBuilder = $this->prophesize(IssuerBuilderInterface::class);
        $client = $this->prophesize(ClientInterface::class);

        $service = new AggregateParser(
            $issuerBuilder->reveal(),
            $algorithmManager->reveal(),
            $JWSVerifier->reveal()
        );

        $claims = [
            'sub' => 'foo',
            '_claim_sources' => [
                'src1' => [
                    'JWT' => 'foo',
                ],
            ],
        ];

        $unpacked = $service->unpack($client->reveal(), $claims);

        static::assertSame($claims, $unpacked);
    }

    public function testUnpackAggregatedClaims(): void
    {
        $jwt = 'eyJhbGciOiJub25lIn0.eyJleWVfY29sb3IiOiAiYmx1ZSIsICJzaG9lX3NpemUiOiA4fQ.';

        $algorithmManager = $this->prophesize(AlgorithmManager::class);
        $JWSVerifier = $this->prophesize(JWSVerifier::class);
        $issuerBuilder = $this->prophesize(IssuerBuilderInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $issuer = $this->prophesize(IssuerInterface::class);

        $client->getIssuer()->willReturn($issuer->reveal());

        $service = new AggregateParser(
            $issuerBuilder->reveal(),
            $algorithmManager->reveal(),
            $JWSVerifier->reveal()
        );

        $claims = [
            'sub' => 'foo',
            '_claim_names' => [
                'eye_color' => 'src1',
                'shoe_size' => 'src1',
            ],
            '_claim_sources' => [
                'src1' => [
                    'JWT' => $jwt,
                ],
            ],
        ];

        $unpacked = $service->unpack($client->reveal(), $claims);

        static::assertSame('blue', $unpacked['eye_color'] ?? null);
        static::assertSame(8, $unpacked['shoe_size'] ?? null);
        static::assertArrayNotHasKey('_claim_names', $unpacked);
        static::assertArrayNotHasKey('_claim_sources', $unpacked);
    }

    public function testUnpackAggregatedClaimsWithSignedJWT(): void
    {
        $jwk = JWKFactory::createRSAKey(2_048, ['alg' => 'RS256', 'use' => 'sig']);
        $jwkPublic = $jwk->toPublic();

        $jwsBuilder = new JWSBuilder(new AlgorithmManager([new RS256()]));
        $serializer = new CompactSerializer();
        $jws = $jwsBuilder->create()
            ->withPayload((string) json_encode([
                'eye_color' => 'blue',
            ]))
            ->addSignature($jwk, ['alg' => 'RS256', 'use' => 'sig'])
            ->build();

        $jwt = $serializer->serialize($jws, 0);

        $algorithmManager = $this->prophesize(AlgorithmManager::class);
        $JWSVerifier = $this->prophesize(JWSVerifier::class);
        $client = $this->prophesize(ClientInterface::class);
        $issuer = $this->prophesize(IssuerInterface::class);
        $issuerMetadata = $this->prophesize(IssuerMetadataInterface::class);
        $issuerBuilder = $this->prophesize(IssuerBuilderInterface::class);
        $issuerJwksProvider = $this->prophesize(JwksProviderInterface::class);

        $algorithm = new RS256();
        $algorithmManager->get('RS256')->willReturn($algorithm);

        $JWSVerifier->verifyWithKey(Argument::type(JWS::class), Argument::that(fn(JWK $key) => $jwkPublic->all() === $key->all()), 0)
            ->willReturn(true);

        $client->getIssuer()->willReturn($issuer->reveal());
        $issuerMetadata->getIssuer()->willReturn('foo-issuer');
        $issuer->getMetadata()->willReturn($issuerMetadata->reveal());
        $issuer->getJwksProvider()->willReturn($issuerJwksProvider->reveal());
        $issuerJwksProvider->getJwks()->willReturn([
            'keys' => [
                $jwkPublic->all(),
            ],
        ]);

        $service = new AggregateParser(
            $issuerBuilder->reveal(),
            $algorithmManager->reveal(),
            $JWSVerifier->reveal()
        );

        $claims = [
            'sub' => 'foo',
            '_claim_names' => [
                'eye_color' => 'src1',
            ],
            '_claim_sources' => [
                'src1' => [
                    'JWT' => $jwt,
                ],
            ],
        ];

        $unpacked = $service->unpack($client->reveal(), $claims);

        static::assertSame('blue', $unpacked['eye_color'] ?? null);
        static::assertArrayNotHasKey('_claim_names', $unpacked);
        static::assertArrayNotHasKey('_claim_sources', $unpacked);
    }
}
