<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\IdToken;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use Throwable;

use function Facile\OpenIDClient\base64url_encode;
use function random_bytes;

/**
 * Request an ID token and verify its 'iat' value.
 *
 * Identify the missing 'iat' value and reject the ID Token after doing ID Token validation.
 */
class RpIdTokenIatTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-id_token-iat';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();
        $uri = $authorizationService->getAuthorizationUri($client, [
            'response_type' => $testInfo->getResponseType(),
            'nonce' => base64url_encode(random_bytes(32)),
        ]);

        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri);

        $params = $authorizationService->getCallbackParams($serverRequest, $client);

        try {
            $authorizationService->callback($client, $params);
            throw new AssertionFailedError('No assertion');
        } catch (Throwable $e) {
            Assert::assertSame('Invalid token provided', $e->getMessage());
            Assert::assertRegExp('/The following claims are mandatory: iat/', $e->getPrevious()->getMessage());
        }
    }
}
