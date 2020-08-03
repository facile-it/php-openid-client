<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ScopeRequestParameter;

use PHPUnit\Framework\Assert;
use function Facile\OpenIDClient\base64url_decode;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Session\AuthSession;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Service\UserInfoService;
use function Facile\OpenIDClient\base64url_encode;
use function var_dump;

class RpScopeUserinfoClaimsTest extends AbstractRpTest
{

    public function getTestId(): string
    {
        return 'rp-scope-userinfo-claims';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        $authorizationService = new AuthorizationService();
        $userInfoService = new UserInfoService();

        $authSession = AuthSession::fromArray([
            'nonce' => base64url_encode(\random_bytes(32)),
        ]);
        $uri = $authorizationService->getAuthorizationUri($client, [
            'scope' => 'openid email',
            'response_type' => $testInfo->getResponseType(),
            'nonce' => $authSession->getNonce(),
        ]);

        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri);

        $params = $authorizationService->getCallbackParams($serverRequest, $client);

        $tokenSet = $authorizationService->callback($client, $params, null, $authSession);

        $accessToken = $tokenSet->getAccessToken();

        if ($accessToken) {
            $userInfo = $userInfoService->getUserInfo($client, $tokenSet);
        } else {
            $userInfo = \json_decode(base64url_decode(\explode('.', $tokenSet->getIdToken())[1]), true);
        }

        Assert::assertArrayHasKey('email', $userInfo);
    }
}
