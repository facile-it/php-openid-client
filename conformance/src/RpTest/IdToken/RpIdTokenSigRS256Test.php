<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\IdToken;

use PHPUnit\Framework\Assert;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use function Facile\OpenIDClient\base64url_encode;

/**
 * Request an signed ID Token. Verify the signature on the ID Token using the keys published by the Issuer.
 *
 * Accept the ID Token after doing ID Token validation.
 */
class RpIdTokenSigRS256Test extends AbstractRpTest
{

    public function getTestId(): string
    {
        return 'rp-id_token-sig-rs256';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo, ['id_token_signed_response_alg' => 'RS256']);

        Assert::assertSame('RS256', $client->getMetadata()->get('id_token_signed_response_alg'));

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();
        $uri = $authorizationService->getAuthorizationUri($client, [
            'response_type' => $testInfo->getResponseType(),
            'nonce' => base64url_encode(\random_bytes(32)),
        ]);

        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri);
        $params = $authorizationService->getCallbackParams($serverRequest, $client);

        $tokenSet = $authorizationService->callback($client, $params);

        Assert::assertNotNull($tokenSet->getIdToken());
    }
}
