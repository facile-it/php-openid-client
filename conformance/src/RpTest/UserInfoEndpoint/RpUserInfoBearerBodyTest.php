<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\UserInfoEndpoint;

use function Facile\OpenIDClient\base64url_encode;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Service\UserInfoService;
use Facile\OpenIDClient\Session\AuthSession;
use PHPUnit\Framework\Assert;
use function random_bytes;

/**
 * Pass the access token as a form-encoded body parameter while doing the UserInfo Request.
 *
 * A UserInfo Response.
 */
class RpUserInfoBearerBodyTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-userinfo-bearer-body';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        $authorizationService = new AuthorizationService();
        $userInfoService = new UserInfoService();

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
        $tokenSet = $authorizationService->callback($client, $params, null, $authSession);

        $userInfo = $userInfoService->getUserInfo($client, $tokenSet, true);

        Assert::assertArrayHasKey('sub', $userInfo);
    }
}
