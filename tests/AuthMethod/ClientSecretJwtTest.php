<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\AuthMethod;

use Facile\OpenIDClient\AuthMethod\ClientSecretJwt;
use function Facile\OpenIDClient\base64url_encode;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadata;
use Facile\OpenIDClientTest\TestCase;
use function http_build_query;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\JWSSerializer;
use function json_decode;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use function time;

class ClientSecretJwtTest extends TestCase
{
    public function testGetSupportedMethod(): void
    {
        $auth = new ClientSecretJwt();
        static::assertSame('client_secret_jwt', $auth->getSupportedMethod());
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
        $jwsBuilder2->withPayload(Argument::that(function (string $payload) {
            $decoded = json_decode($payload, true);

            static::assertIsArray($decoded);

            static::assertArrayHasKey('iss', $decoded);
            static::assertArrayHasKey('sub', $decoded);
            static::assertArrayHasKey('aud', $decoded);
            static::assertArrayHasKey('iat', $decoded);
            static::assertArrayHasKey('exp', $decoded);
            static::assertArrayHasKey('jti', $decoded);

            static::assertSame('bar', $decoded['foo'] ?? null);
            static::assertSame('foo', $decoded['iss'] ?? null);
            static::assertSame('foo', $decoded['sub'] ?? null);
            static::assertSame('https://issuer.com/token', $decoded['aud'] ?? null);
            static::assertLessThanOrEqual(time(), $decoded['iat']);
            static::assertLessThanOrEqual(time() + 60, $decoded['exp']);
            static::assertGreaterThan(time(), $decoded['exp']);

            return true;
        }))
            ->shouldBeCalled()
            ->willReturn($jwsBuilder3->reveal());

        $jwsBuilder3->addSignature(Argument::allOf(
            Argument::type(JWK::class),
            Argument::that(function (JWK $jwk) {
                static::assertSame('oct', $jwk->get('kty'));
                static::assertSame(base64url_encode('bar'), $jwk->get('k'));

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

        static::assertSame($request->reveal(), $result);
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
