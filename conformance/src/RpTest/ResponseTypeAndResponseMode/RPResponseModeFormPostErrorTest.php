<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest\ResponseTypeAndResponseMode;

use Facile\OpenIDClient\ConformanceTest\RpTest\AbstractRpTest;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Exception\OAuth2Exception;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Session\AuthSession;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use Throwable;

use function array_combine;
use function Facile\OpenIDClient\base64url_encode;
use function http_build_query;
use function preg_match_all;
use function random_bytes;

/**
 * Construct and send an Authentication Request with response mode set to form_post, max_age=0 and prompt=none which
 * results in the test suite returning an error because the requested conditions cannot be met.
 *
 * The HTML form post authorization error response is consumed, resulting in an error screen shown to the user.
 */
class RPResponseModeFormPostErrorTest extends AbstractRpTest
{
    public function getTestId(): string
    {
        return 'rp-response_mode-form_post-error';
    }

    public function execute(TestInfo $testInfo): void
    {
        $client = $this->registerClient($testInfo);

        // Get authorization redirect uri
        $authorizationService = new AuthorizationService();

        $authSession = AuthSession::fromArray([
            'nonce' => base64url_encode(random_bytes(32)),
        ]);

        $uri = $authorizationService->getAuthorizationUri($client, [
            'response_type' => $testInfo->getResponseType(),
            'response_mode' => 'form_post',
            'prompt' => 'none',
            'max_age' => 0,
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

        try {
            $authorizationService->getCallbackParams($serverRequest, $client);
            throw new AssertionFailedError('No assertions');
        } catch (OAuth2Exception $e) {
            Assert::assertSame('login_required', $e->getError());
        } catch (Throwable $e) {
            throw new AssertionFailedError('No assertions');
        }
    }
}
