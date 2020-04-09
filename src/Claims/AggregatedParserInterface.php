<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Claims;

use Facile\OpenIDClient\Client\ClientInterface;

interface AggregatedParserInterface
{
    /**
     * @param ClientInterface $client
     * @param array<string, mixed> $claims
     *
     * @return array<string, mixed>
     */
    public function unpack(ClientInterface $client, array $claims): array;
}
