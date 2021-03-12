<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ClientAuthentication;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use PHPUnit\Framework\Assert;

use function Facile\OpenIDClient\base64url_encode;

/**
 * Use the 'client_secret_jwt' method to authenticate at the Authorization Server when using the token endpoint.
 *
 * A Token Response, containing an ID token.
 *
 * @internal
 * @coversNothing
 */
final class RpTokenEndpointClientSecretJwtTest extends AbstractRpTest
{
    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo, [
            'token_endpoint_auth_method' => 'client_secret_jwt',
        ]);

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

    public function getTestId(): string
    {
        return 'rp-token_endpoint-client_secret_jwt';
    }
}
