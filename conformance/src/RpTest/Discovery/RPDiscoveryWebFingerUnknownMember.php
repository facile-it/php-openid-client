<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\Discovery;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use PHPUnit\Framework\Assert;
use function sprintf;

/**
 * The webfinger response will contain a member that the client doesn't recognize.
 *
 * An issuer location should be returned.
 */
class RPDiscoveryWebFingerUnknownMember extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-discovery-webfinger-unknown-member';
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
