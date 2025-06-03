<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ClientAuthentication;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
use PHPUnit\Framework\Assert;

use function Facile\OpenIDClient\base64url_encode;
use function json_decode;
use function json_encode;
use function random_bytes;

/**
 * Use the 'private_key_jwt' method to authenticate at the Authorization Server when using the token endpoint.
 *
 * A Token Response, containing an ID token.
 */
class RpTokenEndpointPrivateKeyJwtTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-token_endpoint-private_key_jwt';
    }

    public function execute(TestInfo $testInfo): void
    {
        $jwk = JWKFactory::createRSAKey(2_048, ['use' => 'sig', 'alg' => 'RS256']);
        $jwks = new JWKSet([$jwk]);
        $publicJwks = new JWKSet([$jwk->toPublic()]);

        $client = $this->registerClient($testInfo, [
            'token_endpoint_auth_method' => 'private_key_jwt',
            'jwks' => json_decode(json_encode($publicJwks), true),
        ], $jwks);

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
