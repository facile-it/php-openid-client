<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Token;

use Facile\JoseVerifier\JWK\JwksProviderInterface;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadataInterface;
use Facile\OpenIDClientTest\TestCase;

/**
 * @psalm-require-extends TestCase
 */
trait ClientInterfaceTrait
{
    public function getClientInterface(): ClientInterface
    {
        $jwksProviderProphecy = $this->prophesize(JwksProviderInterface::class);

        $issuerMetadataProphecy = $this->prophesize(IssuerMetadataInterface::class);
        $issuerMetadataProphecy->toArray()->willReturn(['issuer' => 'issuer']);

        $issuerProphecy = $this->prophesize(IssuerInterface::class);
        $issuerProphecy->getMetadata()->willReturn($issuerMetadataProphecy->reveal());
        $issuerProphecy->getJwksProvider()->willReturn($jwksProviderProphecy->reveal());

        $metadataProphecy = $this->prophesize(ClientMetadataInterface::class);
        $metadataProphecy->toArray()->willReturn(['client_id' => 'client_id']);

        $clientProphecy = $this->prophesize(ClientInterface::class);
        $clientProphecy->getMetadata()->willReturn($metadataProphecy->reveal());
        $clientProphecy->getIssuer()->willReturn($issuerProphecy->reveal());
        $clientProphecy->getJwksProvider()->willReturn($jwksProviderProphecy->reveal());

        return $clientProphecy->reveal();
    }
}
