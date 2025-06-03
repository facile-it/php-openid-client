<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\Discovery;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use PHPUnit\Framework\Assert;

use function sprintf;

/**
 * The Relying Party uses keys from the jwks_uri which has been obtained from the OpenID Provider Metadata.
 *
 * Should be able to verify signed responses and/or encrypt requests using obtained keys.
 */
class RPDiscoveryJwksUriKeys extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-discovery-jwks_uri-keys';
    }

    public function execute(TestInfo $testInfo): void
    {
        $configUri = sprintf('%s/%s/%s/.well-known/openid-configuration', $testInfo->getRoot(), $testInfo->getRpId(), $this->getTestId());
        $issuer = (new IssuerBuilder())
            ->build($configUri);

        Assert::assertCount(4, $issuer->getJwksProvider()->getJwks()['keys'] ?? []);
    }
}
