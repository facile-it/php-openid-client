<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Authorization;

use Facile\OpenIDClient\Authorization\AuthRequest;
use Facile\OpenIDClientTest\TestCase;
use function json_decode;
use function json_encode;

/**
 * @internal
 * @coversNothing
 */
final class AuthRequestTest extends TestCase
{
    public function testCreateParams(): void
    {
        $authRequest = AuthRequest::fromParams([
            'client_id' => 'foo',
            'redirect_uri' => 'bar',
        ]);

        $array = $authRequest->createParams();

        self::assertSame('foo', $array['client_id']);
        self::assertSame('bar', $array['redirect_uri']);
        self::assertSame('openid', $array['scope']);
        self::assertSame('code', $array['response_type']);
        self::assertSame('query', $array['response_mode']);
    }

    public function testFromParams(): void
    {
        $authRequest = AuthRequest::fromParams([
            'scope' => 'fooscope',
            'client_id' => 'foo',
            'redirect_uri' => 'bar',
        ]);

        self::assertSame('foo', $authRequest->getClientId());
        self::assertSame('bar', $authRequest->getRedirectUri());
        self::assertSame('fooscope', $authRequest->getScope());
    }

    public function testGetAcrValues(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getAcrValues());

        $authRequest = new AuthRequest('foo', 'bar', ['acr_values' => 'foo']);
        self::assertSame('foo', $authRequest->getAcrValues());
    }

    public function testGetClientId(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertSame('foo', $authRequest->getClientId());
    }

    public function testGetCodeChallenge(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getCodeChallenge());

        $authRequest = new AuthRequest('foo', 'bar', ['code_challenge' => 'foo']);
        self::assertSame('foo', $authRequest->getCodeChallenge());
    }

    public function testGetCodeChallengeMethod(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getCodeChallengeMethod());

        $authRequest = new AuthRequest('foo', 'bar', ['code_challenge_method' => 'foo']);
        self::assertSame('foo', $authRequest->getCodeChallengeMethod());
    }

    public function testGetDisplay(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getDisplay());

        $authRequest = new AuthRequest('foo', 'bar', ['display' => 'foo']);
        self::assertSame('foo', $authRequest->getDisplay());
    }

    public function testGetIdTokenHint(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getIdTokenHint());

        $authRequest = new AuthRequest('foo', 'bar', ['id_token_hint' => 'foo']);
        self::assertSame('foo', $authRequest->getIdTokenHint());
    }

    public function testGetLoginHint(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getLoginHint());

        $authRequest = new AuthRequest('foo', 'bar', ['login_hint' => 'foo']);
        self::assertSame('foo', $authRequest->getLoginHint());
    }

    public function testGetMaxAge(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getMaxAge());

        $authRequest = new AuthRequest('foo', 'bar', ['max_age' => 3]);
        self::assertSame(3, $authRequest->getMaxAge());
    }

    public function testGetNonce(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getNonce());

        $authRequest = new AuthRequest('foo', 'bar', ['nonce' => 'foo']);
        self::assertSame('foo', $authRequest->getNonce());
    }

    public function testGetPrompt(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getPrompt());

        $authRequest = new AuthRequest('foo', 'bar', ['prompt' => 'foo']);
        self::assertSame('foo', $authRequest->getPrompt());
    }

    public function testGetRedirectUri(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertSame('bar', $authRequest->getRedirectUri());
    }

    public function testGetRequest(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getRequest());

        $authRequest = new AuthRequest('foo', 'bar', ['request' => 'foo']);
        self::assertSame('foo', $authRequest->getRequest());
    }

    public function testGetResponseMode(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertSame('query', $authRequest->getResponseMode());

        $authRequest = new AuthRequest('foo', 'bar', ['response_mode' => 'foo']);
        self::assertSame('foo', $authRequest->getResponseMode());
    }

    public function testGetResponseType(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertSame('code', $authRequest->getResponseType());

        $authRequest = new AuthRequest('foo', 'bar', ['response_type' => 'foo']);
        self::assertSame('foo', $authRequest->getResponseType());
    }

    public function testGetScope(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertSame('openid', $authRequest->getScope());

        $authRequest = new AuthRequest('foo', 'bar', ['scope' => 'foo']);
        self::assertSame('foo', $authRequest->getScope());
    }

    public function testGetState(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getState());

        $authRequest = new AuthRequest('foo', 'bar', ['state' => 'foo']);
        self::assertSame('foo', $authRequest->getState());
    }

    public function testGetUiLocales(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        self::assertNull($authRequest->getUiLocales());

        $authRequest = new AuthRequest('foo', 'bar', ['ui_locales' => 'it_IT']);
        self::assertSame('it_IT', $authRequest->getUiLocales());
    }

    public function testJsonSerialize(): void
    {
        $authRequest = AuthRequest::fromParams([
            'client_id' => 'foo',
            'redirect_uri' => 'bar',
        ]);

        $array = json_decode(json_encode($authRequest), true);

        self::assertSame('foo', $array['client_id']);
        self::assertSame('bar', $array['redirect_uri']);
        self::assertSame('openid', $array['scope']);
        self::assertSame('code', $array['response_type']);
        self::assertSame('query', $array['response_mode']);
    }

    public function testWithParams(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        $authRequest2 = $authRequest->withParams(['request' => 'foo']);

        self::assertNotSame($authRequest2, $authRequest);
        self::assertNull($authRequest->getRequest());
        self::assertSame('foo', $authRequest2->getRequest());
    }
}
