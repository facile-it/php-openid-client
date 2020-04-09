<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\Discovery;

use PHPUnit\Framework\Assert;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Issuer\IssuerBuilder;

/**
 * Use WebFinger (RFC7033) and OpenID Provider Issuer Discovery to determine the location of the OpenID Provider.
 * The discovery should be done using acct URI syntax as user input identifier
 * Note that the local part of the acct value should adhere to the pattern.
 *
 * An issuer location should be returned.
 */
class RPDiscoveryWebFingerAcct extends AbstractRpTest
{

    public function getTestId(): string
    {
        return 'rp-discovery-webfinger-acct';
    }

    public function execute(TestInfo $testInfo): void
    {
        $parsed = \parse_url($testInfo->getRoot());
        $issuerHostAndPort = rtrim($parsed['host'] . ':' . ($parsed['port'] ?? ''), ':');

        $input = sprintf('acct:%s.%s@%s', $testInfo->getRpId(), $this->getTestId(), $issuerHostAndPort);
        $issuer = (new IssuerBuilder())
            ->build($input);

        $expected = sprintf('%s/%s/%s', $testInfo->getRoot(), $testInfo->getRpId(), $this->getTestId());
        Assert::assertSame($expected, $issuer->getMetadata()->getIssuer());
    }
}
