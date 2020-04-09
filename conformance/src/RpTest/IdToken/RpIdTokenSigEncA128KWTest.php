<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\IdToken;

use Jose\Component\Core\JWK;
use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
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
class RpIdTokenSigEncA128KWTest extends AbstractRpTest
{

    public function getTestId(): string
    {
        return 'rp-id_token-sig+enc-a128kw';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo, [
            'id_token_signed_response_alg' => 'RS256',
            'id_token_encrypted_response_alg' => 'A128KW',
            'id_token_encrypted_response_enc' => 'A256CBC-HS512',
        ]);

        Assert::assertSame('RS256', $client->getMetadata()->get('id_token_signed_response_alg'));
        Assert::assertSame('A128KW', $client->getMetadata()->get('id_token_encrypted_response_alg'));
        Assert::assertSame('A256CBC-HS512', $client->getMetadata()->get('id_token_encrypted_response_enc'));

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
        Assert::arrayHasKey('email', $tokenSet->claims());
    }
}
