<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Client\Metadata;

interface MetadataFactoryInterface
{
    /**
     * @param array<string, mixed> $metadata
     *
     * @return ClientMetadataInterface
     *
     * @phpstan-param OpenIDClientMetadata $metadata
     */
    public function fromArray(array $metadata): ClientMetadataInterface;
}
