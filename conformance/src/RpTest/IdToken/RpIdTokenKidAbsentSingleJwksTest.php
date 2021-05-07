<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\IdToken;

use function Facile\OpenIDClient\base64url_encode;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use PHPUnit\Framework\Assert;
use function random_bytes;

/**
 * Request an ID token and verify its signature using a single matching key provided by the Issuer.
 *
 * Use the single matching key out of the Issuer's published set to verify the ID Tokens signature
 * and accept the ID Token after doing ID Token validation.
 */
class RpIdTokenKidAbsentSingleJwksTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-id_token-kid-absent-single-jwks';
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

        $tokenSet = $authorizationService->callback($client, $params);

        Assert::assertNotNull($tokenSet->getIdToken());
    }
}
