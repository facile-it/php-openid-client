<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\AuthMethod;

use Facile\OpenIDClient\AuthMethod\ClientSecretJwt;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadata;
use Facile\OpenIDClientTest\TestCase;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\JWSSerializer;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

use function Facile\OpenIDClient\base64url_encode;
use function http_build_query;
use function json_decode;
use function time;

class ClientSecretJwtTest extends TestCase
{
    public function testGetSupportedMethod(): void
    {
        $auth = new ClientSecretJwt();
        self::assertSame('client_secret_jwt', $auth->getSupportedMethod());
    }

    public function testCreateRequest(): void
    {
        $jwsBuilder = $this->prophesize(JWSBuilder::class);
        $serializer = $this->prophesize(JWSSerializer::class);

        $auth = new ClientSecretJwt(
            $jwsBuilder->reveal(),
            $serializer->reveal()
        );

        $issuerMetadata = IssuerMetadata::fromArray([
            'issuer' => 'https://issuer.com',
            'authorization_endpoint' => 'https://issuer.com/auth',
            'token_endpoint' => 'https://issuer.com/token',
            'jwks_uri' => 'https://issuer.com/jwks',
        ]);

        $clientMetadata = ClientMetadata::fromArray([
            'client_id' => 'foo',
            'client_secret' => 'bar',
        ]);

        $stream = $this->prophesize(StreamInterface::class);
        $request = $this->prophesize(RequestInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $issuer = $this->prophesize(IssuerInterface::class);

        $client->getMetadata()->willReturn($clientMetadata);
        $client->getIssuer()->willReturn($issuer->reveal());
        $issuer->getMetadata()->willReturn($issuerMetadata);

        $jwsBuilder2 = $this->prophesize(JWSBuilder::class);
        $jwsBuilder3 = $this->prophesize(JWSBuilder::class);
        $jwsBuilder4 = $this->prophesize(JWSBuilder::class);
        $jws = $this->prophesize(JWS::class);

        $jwsBuilder->create()->shouldBeCalled()->willReturn($jwsBuilder2->reveal());
        $jwsBuilder2->withPayload(Argument::that(function (string $payload): true {
            $decoded = json_decode($payload, true);

            self::assertIsArray($decoded);

            self::assertArrayHasKey('iss', $decoded);
            self::assertArrayHasKey('sub', $decoded);
            self::assertArrayHasKey('aud', $decoded);
            self::assertArrayHasKey('iat', $decoded);
            self::assertArrayHasKey('exp', $decoded);
            self::assertArrayHasKey('jti', $decoded);

            self::assertSame('bar', $decoded['foo'] ?? null);
            self::assertSame('foo', $decoded['iss'] ?? null);
            self::assertSame('foo', $decoded['sub'] ?? null);
            self::assertSame('https://issuer.com/token', $decoded['aud'] ?? null);
            self::assertLessThanOrEqual(time(), $decoded['iat']);
            self::assertLessThanOrEqual(time() + 60, $decoded['exp']);
            self::assertGreaterThan(time(), $decoded['exp']);

            return true;
        }))
            ->shouldBeCalled()
            ->willReturn($jwsBuilder3->reveal());

        $jwsBuilder3->addSignature(Argument::allOf(
            Argument::type(JWK::class),
            Argument::that(function (JWK $jwk): true {
                self::assertSame('oct', $jwk->get('kty'));
                self::assertSame(base64url_encode('bar'), $jwk->get('k'));

                return true;
            })
        ), Argument::allOf(
            Argument::type('array'),
            Argument::withEntry('alg', 'HS256'),
            Argument::withKey('jti')
        ))
            ->shouldBeCalled()
            ->willReturn($jwsBuilder4);
        $jwsBuilder4->build()->willReturn($jws->reveal());

        $serializer->serialize($jws->reveal(), 0)
            ->shouldBeCalled()
            ->willReturn('assertion');

        $body = http_build_query([
            'client_id' => 'foo',
            'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
            'client_assertion' => 'assertion',
            'foo' => 'bar',
        ]);

        $stream->write($body)->shouldBeCalled();

        $request->getBody()->willReturn($stream->reveal());

        $result = $auth->createRequest(
            $request->reveal(),
            $client->reveal(),
            ['foo' => 'bar']
        );

        self::assertSame($request->reveal(), $result);
    }

    public function testCreateRequestWithNoClientSecret(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $jwsBuilder = $this->prophesize(JWSBuilder::class);
        $serializer = $this->prophesize(JWSSerializer::class);

        $auth = new ClientSecretJwt(
            $jwsBuilder->reveal(),
            $serializer->reveal()
        );

        $request = $this->prophesize(RequestInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);

        $client->getMetadata()->willReturn($metadata->reveal());
        $metadata->getClientId()->willReturn('foo');
        $metadata->getClientSecret()->willReturn(null);

        $auth->createRequest(
            $request->reveal(),
            $client->reveal(),
            []
        );
    }
}
