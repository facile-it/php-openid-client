<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Client;

use Facile\JoseVerifier\JWK\JwksProviderInterface;
use Facile\OpenIDClient\AuthMethod\AuthMethodFactoryInterface;
use Facile\OpenIDClient\Client\Client;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use Facile\OpenIDClientTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ClientTest extends TestCase
{
    public function testMinimalConstructor(): void
    {
        $issuer = $this->prophesize(IssuerInterface::class);
        $metadata = $this->prophesize(ClientMetadataInterface::class);

        $client = new Client(
            $issuer->reveal(),
            $metadata->reveal()
        );

        self::assertSame($issuer->reveal(), $client->getIssuer());
        self::assertSame($metadata->reveal(), $client->getMetadata());
        self::assertInstanceOf(JwksProviderInterface::class, $client->getJwksProvider());
        self::assertInstanceOf(AuthMethodFactoryInterface::class, $client->getAuthMethodFactory());
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

        self::assertSame($issuer->reveal(), $client->getIssuer());
        self::assertSame($metadata->reveal(), $client->getMetadata());
        self::assertSame($jwksProvider->reveal(), $client->getJwksProvider());
        self::assertSame($authMethodFactory->reveal(), $client->getAuthMethodFactory());
    }
}
