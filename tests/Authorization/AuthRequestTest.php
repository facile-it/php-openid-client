<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Authorization;

use Facile\OpenIDClient\Authorization\AuthRequest;
use function json_decode;
use function json_encode;
use PHPUnit\Framework\TestCase;

class AuthRequestTest extends TestCase
{
    public function testFromParams(): void
    {
        $authRequest = AuthRequest::fromParams([
            'scope' => 'fooscope',
            'client_id' => 'foo',
            'redirect_uri' => 'bar',
        ]);

        static::assertSame('foo', $authRequest->getClientId());
        static::assertSame('bar', $authRequest->getRedirectUri());
        static::assertSame('fooscope', $authRequest->getScope());
    }

    public function testJsonSerialize(): void
    {
        $authRequest = AuthRequest::fromParams([
            'client_id' => 'foo',
            'redirect_uri' => 'bar',
        ]);

        $array = json_decode(json_encode($authRequest), true);

        static::assertSame('foo', $array['client_id']);
        static::assertSame('bar', $array['redirect_uri']);
        static::assertSame('openid', $array['scope']);
        static::assertSame('code', $array['response_type']);
        static::assertSame('query', $array['response_mode']);
    }

    public function testCreateParams(): void
    {
        $authRequest = AuthRequest::fromParams([
            'client_id' => 'foo',
            'redirect_uri' => 'bar',
        ]);

        $array = $authRequest->createParams();

        static::assertSame('foo', $array['client_id']);
        static::assertSame('bar', $array['redirect_uri']);
        static::assertSame('openid', $array['scope']);
        static::assertSame('code', $array['response_type']);
        static::assertSame('query', $array['response_mode']);
    }

    public function testGetClientId(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertSame('foo', $authRequest->getClientId());
    }

    public function testGetUiLocales(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getUiLocales());

        $authRequest = new AuthRequest('foo', 'bar', ['ui_locales' => 'it_IT']);
        static::assertSame('it_IT', $authRequest->getUiLocales());
    }

    public function testGetRequest(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getRequest());

        $authRequest = new AuthRequest('foo', 'bar', ['request' => 'foo']);
        static::assertSame('foo', $authRequest->getRequest());
    }

    public function testWithParams(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        $authRequest2 = $authRequest->withParams(['request' => 'foo']);

        static::assertNotSame($authRequest2, $authRequest);
        static::assertNull($authRequest->getRequest());
        static::assertSame('foo', $authRequest2->getRequest());
    }

    public function testGetCodeChallengeMethod(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getCodeChallengeMethod());

        $authRequest = new AuthRequest('foo', 'bar', ['code_challenge_method' => 'foo']);
        static::assertSame('foo', $authRequest->getCodeChallengeMethod());
    }

    public function testGetState(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getState());

        $authRequest = new AuthRequest('foo', 'bar', ['state' => 'foo']);
        static::assertSame('foo', $authRequest->getState());
    }

    public function testGetLoginHint(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getLoginHint());

        $authRequest = new AuthRequest('foo', 'bar', ['login_hint' => 'foo']);
        static::assertSame('foo', $authRequest->getLoginHint());
    }

    public function testGetDisplay(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getDisplay());

        $authRequest = new AuthRequest('foo', 'bar', ['display' => 'foo']);
        static::assertSame('foo', $authRequest->getDisplay());
    }

    public function testGetMaxAge(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getMaxAge());

        $authRequest = new AuthRequest('foo', 'bar', ['max_age' => 3]);
        static::assertSame(3, $authRequest->getMaxAge());
    }

    public function testGetRedirectUri(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertSame('bar', $authRequest->getRedirectUri());
    }

    public function testGetNonce(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getNonce());

        $authRequest = new AuthRequest('foo', 'bar', ['nonce' => 'foo']);
        static::assertSame('foo', $authRequest->getNonce());
    }

    public function testGetCodeChallenge(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getCodeChallenge());

        $authRequest = new AuthRequest('foo', 'bar', ['code_challenge' => 'foo']);
        static::assertSame('foo', $authRequest->getCodeChallenge());
    }

    public function testGetAcrValues(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getAcrValues());

        $authRequest = new AuthRequest('foo', 'bar', ['acr_values' => 'foo']);
        static::assertSame('foo', $authRequest->getAcrValues());
    }

    public function testGetScope(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertSame('openid', $authRequest->getScope());

        $authRequest = new AuthRequest('foo', 'bar', ['scope' => 'foo']);
        static::assertSame('foo', $authRequest->getScope());
    }

    public function testGetResponseMode(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertSame('query', $authRequest->getResponseMode());

        $authRequest = new AuthRequest('foo', 'bar', ['response_mode' => 'foo']);
        static::assertSame('foo', $authRequest->getResponseMode());
    }

    public function testGetPrompt(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getPrompt());

        $authRequest = new AuthRequest('foo', 'bar', ['prompt' => 'foo']);
        static::assertSame('foo', $authRequest->getPrompt());
    }

    public function testGetIdTokenHint(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertNull($authRequest->getIdTokenHint());

        $authRequest = new AuthRequest('foo', 'bar', ['id_token_hint' => 'foo']);
        static::assertSame('foo', $authRequest->getIdTokenHint());
    }

    public function testGetResponseType(): void
    {
        $authRequest = new AuthRequest('foo', 'bar');

        static::assertSame('code', $authRequest->getResponseType());

        $authRequest = new AuthRequest('foo', 'bar', ['response_type' => 'foo']);
        static::assertSame('foo', $authRequest->getResponseType());
    }
}
