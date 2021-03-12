<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ResponseTypeAndResponseMode;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Session\AuthSession;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\Assert;

use function Facile\OpenIDClient\base64url_encode;

/**
 * Make an authentication request with the response_type set to 'id_token token' and the response mode set to form_post.
 *
 * HTML form post response processed, resulting in query encoded parameters.
 *
 * @internal
 * @coversNothing
 */
final class RPResponseModeFormPostTest extends AbstractRpTest
{
    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();

        $authSession = AuthSession::fromArray([
            'nonce' => base64url_encode(random_bytes(32)),
        ]);

        $uri = $authorizationService->getAuthorizationUri($client, [
            'response_mode' => 'form_post',
            'nonce' => $authSession->getNonce(),
        ]);

        // Simulate a redirect and create the server request
        $serverRequestFactory = new ServerRequestFactory();
        $response = $this->httpGet($uri);
        $body = (string) $response->getBody();

        preg_match_all('/<input type="hidden" name="(\w+)" value="([^"]+)"\/>/', $body, $matches);
        $requestBody = http_build_query(array_combine($matches[1], $matches[2]));

        $serverRequest = $serverRequestFactory->createServerRequest('POST', 'http://redirect.dev', [
            'content-type' => 'application/x-www-form-urlencoded',
        ]);
        $serverRequest->getBody()->write($requestBody);

        $params = $authorizationService->getCallbackParams($serverRequest, $client);
        $tokenSet = $authorizationService->callback($client, $params, null, $authSession);

        Assert::assertNotNull($tokenSet->getIdToken());
    }

    public function getTestId(): string
    {
        return 'rp-response_mode-form_post';
    }
}
