<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ClaimsTypes;

use PHPUnit\Framework\Assert;
use Facile\OpenIDClient\Claims\DistributedParser;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Session\AuthSession;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Service\UserinfoService;
use function Facile\OpenIDClient\base64url_encode;

/**
 * Make a UserInfo Request and read the Distributed Claims.
 *
 * Understand the distributed claims in the UserInfo Response.
 */
class RPClaimsDistributedTest extends AbstractRpTest
{

    public function getTestId(): string
    {
        return 'rp-claims-distributed';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        $authorizationService = new AuthorizationService();
        $userInfoService = new UserinfoService();
        $distributedClaims = new DistributedParser();

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

        $userInfo = $userInfoService->getUserInfo($client, $tokenSet);
        $distributed = $distributedClaims->fetch($client, $userInfo);

        Assert::assertArrayHasKey('age', $distributed);
        Assert::assertSame(30, $distributed['age']);
    }
}
