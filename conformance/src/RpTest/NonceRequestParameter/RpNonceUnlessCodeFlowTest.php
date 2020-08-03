<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\NonceRequestParameter;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use Facile\OpenIDClient\Session\AuthSession;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Service\UserInfoService;
use function Facile\OpenIDClient\base64url_encode;

/**
 * Always send a 'nonce' value as a request parameter while using implicit or hybrid flow.
 * Verify the 'nonce' value returned in the ID Token.
 *
 * An ID Token, either from the Authorization Endpoint or from the Token Endpoint, containing the same 'nonce' value
 * as passed in the authentication request when using hybrid flow or implicit flow.
 */
class RpNonceUnlessCodeFlowTest extends AbstractRpTest
{

    public function getTestId(): string
    {
        return 'rp-nonce-unless-code-flow';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        $authorizationService = new AuthorizationService();

        try {
            $authorizationService->getAuthorizationUri($client, [
                'response_type' => $testInfo->getResponseType(),
            ]);

            throw new AssertionFailedError('No assertion');
        } catch (InvalidArgumentException $e) {
            Assert::assertRegExp('/nonce MUST be provided for implicit and hybrid flows/', $e->getMessage());
        }

        $nonce = base64url_encode(\random_bytes(32));
        $authSession = AuthSession::fromArray(['nonce' => $nonce]);

        $uri = $authorizationService->getAuthorizationUri($client, [
            'response_type' => $testInfo->getResponseType(),
            'nonce' => $nonce,
        ]);
        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri);

        $params = $authorizationService->getCallbackParams($serverRequest, $client);
        $tokenSet = $authorizationService->callback($client, $params, null, $authSession);

        Assert::assertTrue(true);
    }
}
