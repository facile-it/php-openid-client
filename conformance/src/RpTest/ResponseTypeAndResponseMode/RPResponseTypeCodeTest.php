<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ResponseTypeAndResponseMode;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use PHPUnit\Framework\Assert;

class RPResponseTypeCodeTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-response_type-code';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        Assert::assertSame('code', $testInfo->getResponseType());

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();
        $uri = $authorizationService->getAuthorizationUri($client, ['response_type' => $testInfo->getResponseType()]);
        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri);

        $params = $authorizationService->getCallbackParams($serverRequest, $client);

        Assert::assertArrayHasKey('code', $params);
    }
}
