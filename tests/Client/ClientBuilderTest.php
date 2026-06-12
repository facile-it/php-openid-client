<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Client;

use Facile\JoseVerifier\JWK\MemoryJwksProvider;
use Facile\OpenIDClient\Client\ClientBuilder;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use Facile\OpenIDClientTest\TestCase;

class ClientBuilderTest extends TestCase
{
    public function testWithoutClientJwks(): void
    {
        $issuer = $this->prophesize(IssuerInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);
        $metadata->getJwks()->willReturn(null);
        $builder = (new ClientBuilder())
            ->setClientMetadata($metadata->reveal())
            ->setIssuer($issuer->reveal());
        $client = $builder->build();
        $client->getJwksProvider();

        self::assertSame($issuer->reveal(), $client->getIssuer());
        self::assertSame($metadata->reveal(), $client->getMetadata());
        self::assertInstanceOf(MemoryJwksProvider::class, $client->getJwksProvider());
        self::assertSame(['keys' => []], $client->getJwksProvider()->getJwks());
    }

    public function testBuildWithClientJwks(): void
    {
        $jwks = [
            'keys' => [
                'kty' => 'RSA',
            ],
        ];
        $issuer = $this->prophesize(IssuerInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);
        $metadata->getJwks()->willReturn($jwks);
        $builder = (new ClientBuilder())
            ->setClientMetadata($metadata->reveal())
            ->setIssuer($issuer->reveal());
        $client = $builder->build();
        $client->getJwksProvider();

        self::assertSame($issuer->reveal(), $client->getIssuer());
        self::assertSame($metadata->reveal(), $client->getMetadata());
        self::assertInstanceOf(MemoryJwksProvider::class, $client->getJwksProvider());
        self::assertSame($jwks, $client->getJwksProvider()->getJwks());
    }
}
