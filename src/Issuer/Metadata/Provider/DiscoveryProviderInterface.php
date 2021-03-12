<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata\Provider;

/**
 * @psalm-type DiscoveryConfigurationObject = array<string, mixed>
 */
interface DiscoveryProviderInterface extends RemoteProviderInterface
{
    /**
     * @return array<string, mixed>
     * @psalm-return DiscoveryConfigurationObject
     */
    public function discovery(string $url): array;
}
