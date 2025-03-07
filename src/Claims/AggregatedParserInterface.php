<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Claims;

use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Token\TokenSetInterface;

/**
 * @psalm-import-type TokenSetClaimsType from TokenSetInterface
 */
interface AggregatedParserInterface
{
    /**
     * @param array<string, mixed> $claims
     *
     * @return array<string, mixed>
     *
     * @psalm-param TokenSetClaimsType $claims
     *
     * @psalm-return TokenSetClaimsType
     */
    public function unpack(ClientInterface $client, array $claims): array;
}
