<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\IdToken;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use PHPUnit\Framework\Assert;

use function Facile\OpenIDClient\base64url_encode;
use function random_bytes;

/**
 * Use Code Flow and retrieve an unsigned ID Token.
 * This test is only applicable when response_type='code'.
 *
 * Accept the ID Token after doing ID Token validation.
 */
class RpIdTokenSigNoneTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-id_token-sig-none';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo, ['id_token_signed_response_alg' => 'none']);

        Assert::assertSame('none', $client->getMetadata()->get('id_token_signed_response_alg'));

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
