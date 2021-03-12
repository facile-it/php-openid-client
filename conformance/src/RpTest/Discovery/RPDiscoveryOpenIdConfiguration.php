<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\Discovery;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use PHPUnit\Framework\Assert;

/**
 * Retrieve and use the OpenID Provider Configuration Information.
 *
 * Read and use the JSON object returned from the OpenID Connect Provider.
 *
 * @internal
 * @coversNothing
 */
final class RPDiscoveryOpenIdConfiguration extends AbstractRpTest
{
    public function execute(TestInfo $testInfo): void
    {
        $configUri = sprintf('%s/%s/%s/.well-known/openid-configuration', $testInfo->getRoot(), $testInfo->getRpId(), $this->getTestId());
        $issuer = (new IssuerBuilder())
            ->build($configUri);

        $expected = sprintf('%s/%s/%s', $testInfo->getRoot(), $testInfo->getRpId(), $this->getTestId());
        Assert::assertSame($expected, $issuer->getMetadata()->getIssuer());
    }

    public function getTestId(): string
    {
        return 'rp-discovery-openid-configuration';
    }
}
