<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\Discovery;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use Throwable;

/**
 * Retrieve OpenID Provider Configuration Information for OpenID Provider from the .well-known/openid-configuration path.
 * Verify that the issuer in the provider configuration matches the one returned by WebFinger.
 *
 * Identify that the issuers are not matching and reject the provider configuration.
 */
class RPDiscoveryIssuerNotMatchingConfig extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-discovery-issuer-not-matching-config';
    }

    public function execute(TestInfo $testInfo): void
    {
        $input = $testInfo->getRoot() . '/' . $testInfo->getRpId() . '/' . $this->getTestId() . '/joe';

        try {
            $issuer = (new IssuerBuilder())
                ->build($input);

            throw new AssertionFailedError('No assertions');
        } catch (Throwable $e) {
            Assert::assertSame('Unable to fetch issuer metadata', $e->getMessage());
            Assert::assertRegExp('/Discovered issuer mismatch/', $e->getPrevious()->getMessage());
        }
    }
}
