<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\AuthMethod;

use Facile\JoseVerifier\JWK\JwksProviderInterface;
use Facile\OpenIDClient\AuthMethod\PrivateKeyJwt;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadataInterface;
use Facile\OpenIDClientTest\TestCase;
use Jose\Component\Core\JWK;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSBuilder;
use Jose\Component\Signature\Serializer\JWSSerializer;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use function http_build_query;
use function json_decode;
use function time;

/**
 * @internal
 * @coversNothing
 */
final class PrivateKeyJwtTest extends TestCase
{
    public function createRequestProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    /**
     * @dataProvider createRequestProvider
     */
    public function testCreateRequest(bool $jwkAsDependency = false): void
    {
        $jwsBuilder = $this->prophesize(JWSBuilder::class);
        $serializer = $this->prophesize(JWSSerializer::class);

        $jwk = JWKFactory::createFromSecret('secret', [
            'alg' => 'ALG',
            'kid' => 'foo',
        ]);

        $auth = new PrivateKeyJwt(
            $jwsBuilder->reveal(),
            $serializer->reveal(),
            $jwkAsDependency ? $jwk : null,
            60
        );

        $jwksProvider = $this->prophesize(JwksProviderInterface::class);

        $stream = $this->prophesize(StreamInterface::class);
        $request = $this->prophesize(RequestInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);
        $issuer = $this->prophesize(IssuerInterface::class);
        $issuerMetadata = $this->prophesize(IssuerMetadataInterface::class);
        $client->getJwksProvider()->willReturn($jwksProvider->reveal());

        if (!$jwkAsDependency) {
            $jwksProvider->getJwks()->willReturn([
                'keys' => [
                    $jwk->all(),
                ],
            ]);
        }

        $client->getMetadata()->willReturn($metadata->reveal());
        $client->getIssuer()->willReturn($issuer->reveal());
        $metadata->getClientId()->willReturn('foo');
        $metadata->getClientSecret()->willReturn('bar');
        $issuer->getMetadata()->willReturn($issuerMetadata->reveal());
        $issuerMetadata->getIssuer()->willReturn('issuer');

        $jwsBuilder2 = $this->prophesize(JWSBuilder::class);
        $jwsBuilder3 = $this->prophesize(JWSBuilder::class);
        $jwsBuilder4 = $this->prophesize(JWSBuilder::class);
        $jws = $this->prophesize(JWS::class);

        $jwsBuilder->create()->shouldBeCalled()->willReturn($jwsBuilder2->reveal());
        $jwsBuilder2->withPayload(Argument::that(static function (string $payload) {
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
            self::assertSame('issuer', $decoded['aud'] ?? null);
            self::assertLessThanOrEqual(time(), $decoded['iat']);
            self::assertLessThanOrEqual(time() + 60, $decoded['exp']);
            self::assertGreaterThan(time(), $decoded['exp']);

            return true;
        }))
            ->shouldBeCalled()
            ->willReturn($jwsBuilder3->reveal());

        $jwsBuilder3->addSignature(
            Argument::allOf(
                Argument::type(JWK::class),
                Argument::that(static function (JWK $key) {
                    return 'foo' === $key->get('kid');
                })
            ),
            Argument::allOf(
                Argument::type('array'),
                Argument::withEntry('alg', 'ALG'),
                Argument::withKey('jti')
            )
        )
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

    public function testGetSupportedMethod(): void
    {
        $jwsBuilder = $this->prophesize(JWSBuilder::class);
        $serializer = $this->prophesize(JWSSerializer::class);

        $auth = new PrivateKeyJwt(
            $jwsBuilder->reveal(),
            $serializer->reveal(),
            null,
            60
        );
        self::assertSame('private_key_jwt', $auth->getSupportedMethod());
    }
}
