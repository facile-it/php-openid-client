<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Issuer;

use Facile\JoseVerifier\JWK\JwksProviderInterface;
use Facile\OpenIDClient\Issuer\Issuer;
use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadataInterface;
use PHPUnit\Framework\TestCase;

class IssuerTest extends TestCase
{
    public function testMinimalConstructor(): void
    {
        $metadata = $this->prophesize(IssuerMetadataInterface::class);
        $jwksProvider = $this->prophesize(JwksProviderInterface::class);

        $issuer = new Issuer(
            $metadata->reveal(),
            $jwksProvider->reveal()
        );

        static::assertSame($metadata->reveal(), $issuer->getMetadata());
        static::assertSame($jwksProvider->reveal(), $issuer->getJwksProvider());
    }
}
