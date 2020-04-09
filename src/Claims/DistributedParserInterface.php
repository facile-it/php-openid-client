<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Claims;

use Facile\OpenIDClient\Client\ClientInterface;

interface DistributedParserInterface
{
    /**
     * @param ClientInterface $client
     * @param array<string, mixed> $claims
     * @param string[] $accessTokens
     *
     * @return array<string, mixed>
     */
    public function fetch(ClientInterface $client, array $claims, array $accessTokens = []): array;
}
