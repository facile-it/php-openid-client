<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\UserInfoEndpoint;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Service\UserInfoService;
use Facile\OpenIDClient\Session\AuthSession;
use Jose\Component\Core\JWK;
use Jose\Component\Core\JWKSet;
use Jose\Component\KeyManagement\JWKFactory;
use PHPUnit\Framework\Assert;

use function Facile\OpenIDClient\base64url_encode;
use function json_decode;
use function json_encode;

/**
 * Request encrypted UserInfo. Decrypt the UserInfo Response.
 *
 * A UserInfo Response.
 *
 * @internal
 * @coversNothing
 */
final class RPUserInfoEncTest extends AbstractRpTest
{
    public function execute(TestInfo $testInfo): void
    {
        $jwkEncAlg = JWKFactory::createRSAKey(2048, ['alg' => 'RSA1_5', 'use' => 'enc']);

        $jwks = new JWKSet([$jwkEncAlg]);
        $publicJwks = new JWKSet(array_map(static function (JWK $jwk) {
            return $jwk->toPublic();
        }, $jwks->all()));

        $client = $this->registerClient($testInfo, [
            'userinfo_signed_response_alg' => 'none',
            'userinfo_encrypted_response_alg' => 'RSA1_5',
            'jwks' => json_decode(json_encode($publicJwks), true),
        ], $jwks);

        Assert::assertSame('none', $client->getMetadata()->get('userinfo_signed_response_alg'));
        Assert::assertSame('RSA1_5', $client->getMetadata()->get('userinfo_encrypted_response_alg'));

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();
        $userInfoService = new UserInfoService();
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

        $userInfo = $userInfoService->getUserInfo($client, $tokenSet);

        Assert::assertArrayHasKey('sub', $userInfo);
    }

    public function getTestId(): string
    {
        return 'rp-userinfo-enc';
    }
}
