<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\KeyRotation;

use Jose\Component\Core\JWK;
use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
use PHPUnit\Framework\Assert;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Session\AuthSession;
use Facile\OpenIDClient\RequestObject\RequestObjectFactory;
use Facile\OpenIDClient\Service\AuthorizationService;
use function Facile\OpenIDClient\base64url_encode;

/**
 * Fetch the issuer's keys from the 'jwks_uri' and make an encrypted authentication request using the issuer's
 * encryption keys.
 * Fetch the issuer's keys from the jwks_uri again, and make a new encrypted request using the rotated encryption keys.
 *
 * A successful authentication response to both authentication requests encrypted using rotated encryption keys.
 */
class RPKeyRotationOPEncKeyTest extends AbstractRpTest
{

    public function getTestId(): string
    {
        return 'rp-key-rotation-op-enc-key';
    }

    public function execute(TestInfo $testInfo): void
    {
        $jwkSig = JWKFactory::createRSAKey(2048, ['alg' => 'RS256', 'use' => 'sig']);
        $jwkEncAlg = JWKFactory::createRSAKey(2048, ['alg' => 'RSA-OAEP', 'use' => 'enc']);

        $jwks = new JWKSet([$jwkSig, $jwkEncAlg]);
        $publicJwks = new JWKSet(\array_map(static function (JWK $jwk) {
            return $jwk->toPublic();
        }, $jwks->all()));

        $client = $this->registerClient($testInfo, [
            'request_object_signing_alg' => 'RS256',
            'request_object_encryption_alg' => 'RSA-OAEP',
            'request_object_encryption_enc' => 'A128CBC-HS256',
            'jwks' => $publicJwks->jsonSerialize(),
        ], $jwks);

        Assert::assertSame('RS256', $client->getMetadata()->get('request_object_signing_alg'));
        Assert::assertSame('RSA-OAEP', $client->getMetadata()->get('request_object_encryption_alg'));
        Assert::assertSame('A128CBC-HS256', $client->getMetadata()->get('request_object_encryption_enc'));

        // Get authorization redirect uri
        $requestObjectFactory = new RequestObjectFactory();
        $authorizationService = new AuthorizationService();

        $authSession = AuthSession::fromArray([
            'state' => base64url_encode(\random_bytes(32)),
            'nonce' => base64url_encode(\random_bytes(32)),
        ]);
        $uri = $authorizationService->getAuthorizationUri($client, [
            'request' => $requestObjectFactory->create($client),
            'state' => $authSession->getState(),
            'nonce' => $authSession->getNonce(),
        ]);

        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri, 'application/jwt');

        $params = $authorizationService->getCallbackParams($serverRequest, $client);
        $tokenSet = $authorizationService->callback($client, $params, null, $authSession);

        Assert::assertNotNull($tokenSet->getState());

        // update issuer JWKSet
        $client->getIssuer()->getJwksProvider()->reload();

        $uri = $authorizationService->getAuthorizationUri($client, [
            'request' => $requestObjectFactory->create($client),
            'state' => $authSession->getState(),
            'nonce' => $authSession->getNonce(),
        ]);

        // Simulate a redirect and create the server request
        $serverRequest = $this->simulateAuthRedirect($uri);

        $params = $authorizationService->getCallbackParams($serverRequest, $client);
        $tokenSet = $authorizationService->callback($client, $params, null, $authSession);

        Assert::assertNotNull($tokenSet->getIdToken());
    }
}
