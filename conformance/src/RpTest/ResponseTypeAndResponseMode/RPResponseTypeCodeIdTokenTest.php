<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ResponseTypeAndResponseMode;

use PHPUnit\Framework\Assert;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Session\AuthSession;
use Facile\OpenIDClient\Service\AuthorizationService;
use function Facile\OpenIDClient\base64url_encode;

class RPResponseTypeCodeIdTokenTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-response_type-code+id_token';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        Assert::assertSame('code id_token', $testInfo->getResponseType());

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();

        $authSession = AuthSession::fromArray([
            'nonce' => base64url_encode(\random_bytes(32)),
        ]);
        $uri = $authorizationService->getAuthorizationUri($client, [
            'response_type' => $testInfo->getResponseType(),
            'nonce' => $authSession->getNonce(),
        ]);

        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri);

        $params = $authorizationService->getCallbackParams($serverRequest, $client);
        Assert::assertArrayHasKey('code', $params);
        Assert::assertArrayHasKey('id_token', $params);
    }
}
