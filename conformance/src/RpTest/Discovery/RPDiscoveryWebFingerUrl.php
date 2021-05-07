<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\Discovery;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use PHPUnit\Framework\Assert;
use function sprintf;

/**
 * Use WebFinger (RFC7033) and OpenID Provider Issuer Discovery to determine the location of the OpenID Provider.
 * The discovery should be done using URL syntax as user input identifier.
 *
 * An issuer location should be returned.
 */
class RPDiscoveryWebFingerUrl extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-discovery-webfinger-url';
    }

    public function execute(TestInfo $testInfo): void
    {
        $input = $testInfo->getRoot() . '/' . $testInfo->getRpId() . '/' . $this->getTestId() . '/joe';

        $issuer = (new IssuerBuilder())
            ->build($input);

        $expected = sprintf('%s/%s/%s', $testInfo->getRoot(), $testInfo->getRpId(), $this->getTestId());
        Assert::assertSame($expected, $issuer->getMetadata()->getIssuer());
    }
}
