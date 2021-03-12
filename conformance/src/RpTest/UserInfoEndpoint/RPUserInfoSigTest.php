<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\UserInfoEndpoint;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Service\UserInfoService;
use Facile\OpenIDClient\Session\AuthSession;
use PHPUnit\Framework\Assert;

use function Facile\OpenIDClient\base64url_encode;

/**
 * Request signed UserInfo.
 *
 * Successful signature verification of the UserInfo Response.
 *
 * @internal
 * @coversNothing
 */
final class RPUserInfoSigTest extends AbstractRpTest
{
    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo, [
            'userinfo_signed_response_alg' => 'RS256',
        ]);

        Assert::assertSame('RS256', $client->getMetadata()->get('userinfo_signed_response_alg'));

        // Get authorization redirect uri
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

        $userInfo = $userInfoService->getUserInfo($client, $tokenSet);

        Assert::assertArrayHasKey('sub', $userInfo);
    }

    public function getTestId(): string
    {
        return 'rp-userinfo-sig';
    }
}
