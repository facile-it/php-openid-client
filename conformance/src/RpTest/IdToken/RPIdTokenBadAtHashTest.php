<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\IdToken;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Session\AuthSession;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use Throwable;

use function Facile\OpenIDClient\base64url_encode;

/**
 * Make an authentication request using response_type='id_token token' for Implicit Flow or
 * response_type='code id_token token' for Hybrid Flow. Verify the 'at_hash' value in the returned ID Token.
 *
 * Identify the incorrect 'at_hash' value and reject the ID Token after doing Access Token validation.
 *
 * @internal
 * @coversNothing
 */
final class RPIdTokenBadAtHashTest extends AbstractRpTest
{
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
            Assert::assertRegExp('/at_hash mismatch/', $e->getPrevious()->getMessage());
        }
    }

    public function getTestId(): string
    {
        return 'rp-id_token-bad-at_hash';
    }
}
