<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Claims;

use Facile\OpenIDClient\Client\ClientInterface;
use Override;

use function array_key_exists;

/**
 * @psalm-api
 */
final class AggregateParser extends AbstractClaims implements AggregatedParserInterface
{
    #[Override]
    public function unpack(ClientInterface $client, array $claims): array
    {
        if (! array_key_exists('_claim_sources', $claims)) {
            return $claims;
        }

        if (! array_key_exists('_claim_names', $claims)) {
            return $claims;
        }

        $claimPayloads = [];
        foreach ($claims['_claim_sources'] as $sourceName => $source) {
            if (! $this->isAggregateSource($source)) {
                continue;
            }

            $claimPayloads[$sourceName] = $this->claimJWT($client, $source['JWT']);
            unset($claims['_claim_sources'][$sourceName]);
        }

        return $this->cleanClaims($this->assignClaims($claims, $claims['_claim_names'], $claimPayloads));
    }
}
