<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\UserInfoEndpoint;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Session\AuthSession;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Service\UserInfoService;
use function Facile\OpenIDClient\base64url_encode;

/**
 * Make a UserInfo Request and verify the 'sub' value of the UserInfo Response by comparing it with the ID Token's 'sub' value.
 *
 * Identify the invalid 'sub' value and reject the UserInfo Response.
 */
class RpUserInfoBadSubClaimTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-userinfo-bad-sub-claim';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();
        $userInfoService = new UserInfoService();

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
        $tokenSet = $authorizationService->callback($client, $params, null, $authSession);

        try {
            $userInfoService->getUserInfo($client, $tokenSet);
            throw new AssertionFailedError('No assertions');
        } catch (\Throwable $e) {
            Assert::assertRegExp('/Userinfo sub mismatch/', $e->getMessage());
        }
    }
}
