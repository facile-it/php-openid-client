<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer;

use Facile\JoseVerifier\JWK\JwksProviderInterface;
use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadataInterface;
use Override;

final readonly class Issuer implements IssuerInterface
{
    public function __construct(
        private IssuerMetadataInterface $metadata,
        private JwksProviderInterface $jwksProvider
    ) {}

    #[Override]
    public function getMetadata(): IssuerMetadataInterface
    {
        return $this->metadata;
    }

    #[Override]
    public function getJwksProvider(): JwksProviderInterface
    {
        return $this->jwksProvider;
    }
}
