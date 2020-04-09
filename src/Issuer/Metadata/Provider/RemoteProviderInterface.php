<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata\Provider;

interface RemoteProviderInterface
{
    public function isAllowedUri(string $uri): bool;

    /**
     * @param string $uri
     *
     * @return array<string, mixed>
     * @phpstan-return OpenIDDiscoveryConfiguration
     */
    public function fetch(string $uri): array;
}
