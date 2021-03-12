<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer;

use Facile\JoseVerifier\JWK\JwksProviderBuilder;
use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadata;
use Facile\OpenIDClient\Issuer\Metadata\Provider\MetadataProviderBuilder;

final class IssuerBuilder implements IssuerBuilderInterface
{
    /**
     * @var JwksProviderBuilder|null
     */
    private $jwksProviderBuilder;

    /**
     * @var MetadataProviderBuilder|null
     */
    private $metadataProviderBuilder;

    public function build(string $resource): IssuerInterface
    {
        $metadataBuilder = $this->buildMetadataProviderBuilder();
        $metadata = IssuerMetadata::fromArray($metadataBuilder->build()->fetch($resource));

        $jwksProviderBuilder = $this->buildJwksProviderBuilder();
        $jwksProviderBuilder->setJwksUri($metadata->getJwksUri());
        $jwksProvider = $jwksProviderBuilder->build();

        return new Issuer(
            $metadata,
            $jwksProvider
        );
    }

    public function setJwksProviderBuilder(?JwksProviderBuilder $jwksProviderBuilder): self
    {
        $this->jwksProviderBuilder = $jwksProviderBuilder;

        return $this;
    }

    public function setMetadataProviderBuilder(?MetadataProviderBuilder $metadataProviderBuilder): self
    {
        $this->metadataProviderBuilder = $metadataProviderBuilder;

        return $this;
    }

    private function buildJwksProviderBuilder(): JwksProviderBuilder
    {
        return $this->jwksProviderBuilder ?? new JwksProviderBuilder();
    }

    private function buildMetadataProviderBuilder(): MetadataProviderBuilder
    {
        return $this->metadataProviderBuilder ?? new MetadataProviderBuilder();
    }
}
