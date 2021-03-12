<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Authorization;

use JsonSerializable;

interface AuthRequestInterface extends JsonSerializable
{
    /**
     * Create params ready to use.
     *
     * @return array<string, mixed>
     */
    public function createParams(): array;

    /**
     * Requested Authentication Context Class Reference values.
     * Space-separated string that specifies the acr values that the Authorization Server is being requested
     * to use for processing this Authentication Request.
     */
    public function getAcrValues(): ?string;

    /**
     * OAuth 2.0 Client Identifier valid at the Authorization Server.
     */
    public function getClientId(): string;

    public function getCodeChallenge(): ?string;

    public function getCodeChallengeMethod(): ?string;

    /**
     * ASCII string value that specifies how the Authorization Server displays the authentication and consent
     * user interface pages to the End-User.
     *
     * The defined values are:
     * - page
     * - popup
     * - touch
     * - wrap
     */
    public function getDisplay(): ?string;

    /**
     * ID Token previously issued by the Authorization Server being passed as a hint about the End-User's current or
     * past authenticated session with the Client.
     */
    public function getIdTokenHint(): ?string;

    /**
     * Hint to the Authorization Server about the login identifier the End-User might use to log in (if necessary).
     */
    public function getLoginHint(): ?string;

    /**
     * Maximum Authentication Age. Specifies the allowable elapsed time in seconds since the last time the End-User
     * was actively authenticated by the OP.
     */
    public function getMaxAge(): ?int;

    /**
     * String value used to associate a Client session with an ID Token, and to mitigate replay attacks.
     */
    public function getNonce(): ?string;

    /**
     * Space delimited case sensitive list of ASCII string values that specifies whether the Authorization Server prompts
     * the End-User for reauthentication and consent.
     *
     * The defined values are:
     * - none
     * - login
     * - consent
     * - select_account
     */
    public function getPrompt(): ?string;

    /**
     * Redirection URI to which the response will be sent.
     */
    public function getRedirectUri(): string;

    public function getRequest(): ?string;

    /**
     * Informs the Authorization Server of the mechanism to be used for returning parameters from
     * the Authorization Endpoint.
     */
    public function getResponseMode(): ?string;

    /**
     * OAuth 2.0 Response Type value that determines the authorization processing flow to be used,
     * including what parameters are returned from the endpoints used. When using the Authorization Code Flow,
     * this value is code.
     */
    public function getResponseType(): string;

    /**
     * Space delimited scopes. OpenID Connect requests MUST contain the openid scope value.
     */
    public function getScope(): string;

    /**
     * Opaque value used to maintain state between the request and the callback.
     */
    public function getState(): ?string;

    /**
     * End-User's preferred languages and scripts for the user interface, represented as a space-separated list
     * of BCP47 [RFC5646] language tag values, ordered by preference.
     */
    public function getUiLocales(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array;

    /**
     * Add other params and return a new instance.
     *
     * @param array<string, mixed> $params
     *
     * @return AuthRequestInterface
     */
    public function withParams(array $params): self;
}
