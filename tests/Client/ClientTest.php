<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Client;

use Facile\JoseVerifier\JWK\JwksProviderInterface;
use Facile\OpenIDClient\AuthMethod\AuthMethodFactoryInterface;
use Facile\OpenIDClient\Client\Client;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testMinimalConstructor(): void
    {
        $issuer = $this->prophesize(IssuerInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);

        $client = new Client(
            $issuer->reveal(),
            $metadata->reveal()
        );

        static::assertSame($issuer->reveal(), $client->getIssuer());
        static::assertSame($metadata->reveal(), $client->getMetadata());
        static::assertInstanceOf(JwksProviderInterface::class, $client->getJwksProvider());
        static::assertInstanceOf(AuthMethodFactoryInterface::class, $client->getAuthMethodFactory());
    }

    public function testWithFullConstructor(): void
    {
        $issuer = $this->prophesize(IssuerInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);
        $jwksProvider = $this->prophesize(JwksProviderInterface::class);
        $authMethodFactory = $this->prophesize(AuthMethodFactoryInterface::class);

        $client = new Client(
            $issuer->reveal(),
            $metadata->reveal(),
            $jwksProvider->reveal(),
            $authMethodFactory->reveal()
        );

        static::assertSame($issuer->reveal(), $client->getIssuer());
        static::assertSame($metadata->reveal(), $client->getMetadata());
        static::assertSame($jwksProvider->reveal(), $client->getJwksProvider());
        static::assertSame($authMethodFactory->reveal(), $client->getAuthMethodFactory());
    }
}
