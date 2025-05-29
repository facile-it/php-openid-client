<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Claims;

use function array_filter;
use function array_key_exists;
use Facile\OpenIDClient\Client\ClientInterface;

final class AggregateParser extends AbstractClaims implements AggregatedParserInterface
{
    public function unpack(ClientInterface $client, array $claims): array
    {
        if (! array_key_exists('_claim_sources', $claims)) {
            return $claims;
        }

        if (! array_key_exists('_claim_names', $claims)) {
            return $claims;
        }

        $aggregatedSources = array_filter($claims['_claim_sources'], fn ($value): bool => $this->isAggregateSource($value));

        $claimPayloads = [];
        foreach ($aggregatedSources as $sourceName => $source) {
            $claimPayloads[$sourceName] = $this->claimJWT($client, $source['JWT']);
            unset($claims['_claim_sources'][$sourceName]);
        }

        return $this->cleanClaims($this->assignClaims($claims, $claims['_claim_names'], $claimPayloads));
    }
}
