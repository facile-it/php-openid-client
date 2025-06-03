<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\IdToken;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Session\AuthSession;
use Jose\Component\Core\JWK;
use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
use PHPUnit\Framework\Assert;

use function array_map;
use function Facile\OpenIDClient\base64url_encode;
use function json_decode;
use function json_encode;
use function random_bytes;

/**
 * Request an signed ID Token. Verify the signature on the ID Token using the keys published by the Issuer.
 *
 * Accept the ID Token after doing ID Token validation.
 */
class RpIdTokenSigEncTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-id_token-sig+enc';
    }

    public function execute(TestInfo $testInfo): void
    {
        $jwkSig = JWKFactory::createRSAKey(2_048, ['alg' => 'RS256', 'use' => 'sig']);
        $jwkEncAlg = JWKFactory::createRSAKey(2_048, ['alg' => 'RSA1_5', 'use' => 'enc']);

        $jwks = new JWKSet([$jwkSig, $jwkEncAlg]);
        $publicJwks = new JWKSet(array_map(fn(JWK $jwk) => $jwk->toPublic(), $jwks->all()));

        $client = $this->registerClient($testInfo, [
            'id_token_signed_response_alg' => 'RS256',
            'id_token_encrypted_response_alg' => 'RSA1_5',
            'jwks' => json_decode(json_encode($publicJwks), true),
        ], $jwks);

        Assert::assertSame('RS256', $client->getMetadata()->get('id_token_signed_response_alg'));
        Assert::assertSame('RSA1_5', $client->getMetadata()->get('id_token_encrypted_response_alg'));
        Assert::assertSame('A128CBC-HS256', $client->getMetadata()->get('id_token_encrypted_response_enc'));

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

        $tokenSet = $authorizationService->callback($client, $params, null, $authSession);

        Assert::assertNotNull($tokenSet->getIdToken());
        Assert::arrayHasKey('email', $tokenSet->claims());
    }
}
