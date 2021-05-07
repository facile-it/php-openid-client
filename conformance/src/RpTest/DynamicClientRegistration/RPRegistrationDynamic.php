<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\DynamicClientRegistration;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use Facile\OpenIDClient\Service\RegistrationService;
use PHPUnit\Framework\Assert;
use function sprintf;

/**
 * Use the client registration endpoint in order to dynamically register the Relying Party.
 *
 * Get a Client Registration Response.
 */
class RPRegistrationDynamic extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-registration-dynamic';
    }

    public function execute(TestInfo $testInfo): void
    {
        $clientRegistrationService = new RegistrationService();

        $configUri = sprintf('%s/%s/%s/.well-known/openid-configuration', $testInfo->getRoot(), $testInfo->getRpId(), $this->getTestId());
        $issuer = (new IssuerBuilder())
            ->build($configUri);

        $metadata = $clientRegistrationService->register($issuer, [
            'client_name' => $testInfo->getRpId() . '/' . $this->getTestId(),
            'redirect_uris' => [
                'https://example.com/callback',
            ],
            'contacts' => [
                'foo@example.com',
            ],
        ]);

        Assert::assertArrayHasKey('client_id', $metadata);
        Assert::assertArrayHasKey('client_secret_expires_at', $metadata);
    }
}
