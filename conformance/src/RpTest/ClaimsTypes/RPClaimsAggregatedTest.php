<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ClaimsTypes;

use function Facile\OpenIDClient\base64url_encode;
use Facile\OpenIDClient\Claims\AggregateParser;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Service\UserInfoService;
use Facile\OpenIDClient\Session\AuthSession;
use PHPUnit\Framework\Assert;
use function random_bytes;

/**
 * Make a UserInfo Request and read the Aggregated Claims.
 *
 * Understand the aggregated claims in the UserInfo Response.
 */
class RPClaimsAggregatedTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-claims-aggregated';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        $authorizationService = new AuthorizationService();
        $userInfoService = new UserInfoService();
        $aggregatedClaims = new AggregateParser();

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
        $aggregated = $aggregatedClaims->unpack($client, $userInfo);

        Assert::assertArrayHasKey('shoe_size', $aggregated);
        Assert::assertArrayHasKey('eye_color', $aggregated);

        Assert::assertSame(8, $aggregated['shoe_size']);
        Assert::assertSame('blue', $aggregated['eye_color']);
    }
}
