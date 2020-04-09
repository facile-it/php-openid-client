<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Authorization;

use function array_diff;
use function array_diff_key;
use function array_flip;
use function array_keys;
use function array_merge;
use function count;
use function implode;
use Facile\OpenIDClient\Exception\InvalidArgumentException;

final class AuthRequest implements AuthRequestInterface
{
    /** @var array<string, mixed> */
    private $params;

    /** @var string[] */
    private static $requiredKeys = [
        'client_id',
        'redirect_uri',
    ];

    /**
     * @param string $clientId
     * @param string $redirectUri
     * @param array<string, mixed> $params
     */
    public function __construct(
        string $clientId,
        string $redirectUri,
        array $params = []
    ) {
        $defaults = [
            'scope' => 'openid',
            'response_type' => 'code',
            'response_mode' => 'query',
        ];
        /** @var array<string, mixed> $merged */
        $merged = array_merge($defaults, $params);

        $this->params = $merged;
        $this->params['client_id'] = $clientId;
        $this->params['redirect_uri'] = $redirectUri;
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return static
     *
     * @phpstan-param array{client_id: string, redirect_uri: string} $params
     */
    public static function fromParams(array $params): self
    {
        $missingKeys = array_diff(self::$requiredKeys, array_keys($params));
        if (0 !== count($missingKeys)) {
            throw new InvalidArgumentException(implode(', ', $missingKeys) . ' keys not provided');
        }

        return new static(
            $params['client_id'],
            $params['redirect_uri'],
            $params
        );
    }

    /**
     * OpenID Connect requests MUST contain the openid scope value.
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->params['scope'];
    }

    /**
     * OAuth 2.0 Response Type value that determines the authorization processing flow to be used,
     * including what parameters are returned from the endpoints used. When using the Authorization Code Flow,
     * this value is code.
     *
     * @return string
     */
    public function getResponseType(): string
    {
        return $this->params['response_type'];
    }

    /**
     * OAuth 2.0 Client Identifier valid at the Authorization Server.
     *
     * @return string
     */
    public function getClientId(): string
    {
        return $this->params['client_id'];
    }

    /**
     * Redirection URI to which the response will be sent.
     *
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->params['redirect_uri'];
    }

    /**
     * Opaque value used to maintain state between the request and the callback.
     *
     * @return null|string
     */
    public function getState(): ?string
    {
        return $this->params['state'] ?? null;
    }

    /**
     * Informs the Authorization Server of the mechanism to be used for returning parameters from
     * the Authorization Endpoint.
     *
     * @return string|null
     */
    public function getResponseMode(): ?string
    {
        return $this->params['response_mode'] ?? null;
    }

    /**
     * String value used to associate a Client session with an ID Token, and to mitigate replay attacks.
     *
     * @return null|string
     */
    public function getNonce(): ?string
    {
        return $this->params['nonce'] ?? null;
    }

    /**
     * ASCII string value that specifies how the Authorization Server displays the authentication and consent
     * user interface pages to the End-User.
     *
     * The defined values are:
     * - page
     * - popup
     * - touch
     * - wrap
     *
     * @return string|null
     */
    public function getDisplay(): ?string
    {
        return $this->params['display'] ?? null;
    }

    /**
     * Case sensitive list of ASCII string values that specifies whether the Authorization Server prompts
     * the End-User for reauthentication and consent.
     *
     * The defined values are:
     * - none
     * - login
     * - consent
     * - select_account
     *
     * @return null|string
     */
    public function getPrompt(): ?string
    {
        return $this->params['prompt'] ?? null;
    }

    /**
     * Maximum Authentication Age. Specifies the allowable elapsed time in seconds since the last time the End-User
     * was actively authenticated by the OP.
     *
     * @return int|null
     */
    public function getMaxAge(): ?int
    {
        return $this->params['max_age'] ?? null;
    }

    /**
     * End-User's preferred languages and scripts for the user interface, represented as a space-separated list
     * of BCP47 [RFC5646] language tag values, ordered by preference.
     *
     * @return null|string
     */
    public function getUiLocales(): ?string
    {
        return $this->params['ui_locales'] ?? null;
    }

    /**
     * ID Token previously issued by the Authorization Server being passed as a hint about the End-User's current or
     * past authenticated session with the Client.
     *
     * @return string|null
     */
    public function getIdTokenHint(): ?string
    {
        return $this->params['id_token_hint'] ?? null;
    }

    /**
     * Hint to the Authorization Server about the login identifier the End-User might use to log in (if necessary).
     *
     * @return string|null
     */
    public function getLoginHint(): ?string
    {
        return $this->params['login_hint'] ?? null;
    }

    /**
     * Requested Authentication Context Class Reference values.
     *
     * @return null|string
     */
    public function getAcrValues(): ?string
    {
        return $this->params['acr_values'] ?? null;
    }

    public function getRequest(): ?string
    {
        return $this->params['request'] ?? null;
    }

    public function getCodeChallenge(): ?string
    {
        return $this->params['code_challenge'] ?? null;
    }

    public function getCodeChallengeMethod(): ?string
    {
        return $this->params['code_challenge_method'] ?? null;
    }

    /**
     * Add other params and return a new instance.
     *
     * @param array<string, mixed> $params
     *
     * @return AuthRequestInterface
     */
    public function withParams(array $params): AuthRequestInterface
    {
        $instance = clone $this;
        $instance->params = array_merge($instance->params, $params);

        if (0 === count(array_diff_key($instance->params, array_flip(self::$requiredKeys)))) {
            throw new InvalidArgumentException(implode(', ', self::$requiredKeys) . ' should be provided');
        }

        return $instance;
    }

    /**
     * Create params ready to use.
     *
     * @return array<string, mixed>
     */
    public function createParams(): array
    {
        return $this->params;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->createParams();
    }
}
