<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\IdToken;

use function Facile\OpenIDClient\base64url_encode;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Session\AuthSession;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use function random_bytes;
use Throwable;

/**
 * Retrieve Authorization Code and ID Token from the Authorization Endpoint, using Hybrid Flow.
 * Verify the c_hash value in the returned ID token. 'id_token_signed_response_alg' must NOT be 'none'
 *
 * Identify the incorrect 'c_hash' value and reject the ID Token after doing Authorization Code Validation.
 */
class RpIdTokenBadCHashTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-id_token-bad-c_hash';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

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

        try {
            $authorizationService->callback($client, $params, null, $authSession);
            throw new AssertionFailedError('No assertion');
        } catch (Throwable $e) {
            Assert::assertSame('Invalid token provided', $e->getMessage());
            Assert::assertRegExp('/c_hash mismatch/', $e->getPrevious()->getMessage());
        }
    }
}
