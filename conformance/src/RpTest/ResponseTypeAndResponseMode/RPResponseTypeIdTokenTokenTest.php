<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ResponseTypeAndResponseMode;

use function Facile\OpenIDClient\base64url_encode;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Session\AuthSession;
use PHPUnit\Framework\Assert;
use function random_bytes;

class RPResponseTypeIdTokenTokenTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-response_type-id_token+token';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        Assert::assertSame('id_token token', $testInfo->getResponseType());

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();

        $authSession = AuthSession::fromArray([
            'nonce' => base64url_encode(random_bytes(32)),
        ]);
        $uri = $authorizationService->getAuthorizationUri($client, [
            'response_type' => $testInfo->getResponseType(),
            'nonce' => $authSession->getNonce(),
        ]);

        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri);

        $params = $authorizationService->getCallbackParams($serverRequest, $client);
        Assert::assertArrayHasKey('id_token', $params);
        Assert::assertArrayHasKey('access_token', $params);
    }
}
