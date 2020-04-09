<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\NonceRequestParameter;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Session\AuthSession;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Service\UserinfoService;
use function Facile\OpenIDClient\base64url_encode;

class RpNonceInvalidTest extends AbstractRpTest
{

    public function getTestId(): string
    {
        return 'rp-nonce-invalid';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        $authorizationService = new AuthorizationService();
        $userInfoService = new UserinfoService();
        $nonce = base64url_encode(\random_bytes(32));
        $authSession = AuthSession::fromArray(['nonce' => $nonce]);

        $uri = $authorizationService->getAuthorizationUri($client, [
            'response_type' => $testInfo->getResponseType(),
            'nonce' => $nonce,
        ]);
        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri);

        try {
            $params = $authorizationService->getCallbackParams($serverRequest, $client);
            $tokenSet = $authorizationService->callback($client, $params, null, $authSession);
            $accessToken = $tokenSet->getAccessToken();

            if ($accessToken) {
                $userInfoService->getUserInfo($client, $tokenSet);
            }

            throw new AssertionFailedError('No assertion');
        } catch (\Throwable $e) {
            Assert::assertSame('Invalid token provided', $e->getMessage());
            Assert::assertRegExp('/Nonce mismatch.* got: 012345678/', $e->getPrevious()->getMessage());
        }
    }
}
