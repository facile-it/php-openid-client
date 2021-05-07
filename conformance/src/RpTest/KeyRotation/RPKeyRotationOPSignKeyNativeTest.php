<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\KeyRotation;

use function Facile\OpenIDClient\base64url_encode;
use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Session\AuthSession;
use PHPUnit\Framework\Assert;
use function random_bytes;

/**
 * Request an ID Token and verify its signature.
 * Will have to retrieve new keys from the OP to be able to verify the ID Token
 *
 * Successfully verify the ID Token signature, fetching the rotated signing keys if the 'kid' claim in the
 * JOSE header is unknown.
 */
class RPKeyRotationOPSignKeyNativeTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-key-rotation-op-sign-key-native';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();
        $authSession = AuthSession::fromArray([
            'state' => base64url_encode(random_bytes(32)),
            'nonce' => base64url_encode(random_bytes(32)),
        ]);
        $uri = $authorizationService->getAuthorizationUri($client, [
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
