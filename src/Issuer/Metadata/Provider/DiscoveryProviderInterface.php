<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata\Provider;

interface DiscoveryProviderInterface extends RemoteProviderInterface
{
    /**
     * @param string $url
     *
     * @return array<string, mixed>
     * @phpstan-return OpenIDDiscoveryConfiguration
     */
    public function discovery(string $url): array;
}
