<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Token;

/**
 * @psalm-type TokenSetType = array{code?: string, state?: string, token_type?: string, access_token?: string, id_token?: string, refresh_token?: string, expires_in?: int, code_verifier?: string}
 * @psalm-type TokenSetClaimsType = array{sub?: string, _claim_names?: array<string, string>, _claim_sources?: array<string, array{JWT?: string, endpoint?: string, access_token?: string}>}
 * @psalm-type TokenSetMixedType = array{claims?: TokenSetClaimsType}&TokenSetType
 */
interface TokenSetInterface
{
    /**
     * @return array<string, mixed>
     * @psalm-return TokenSetClaimsType
     */
    public function claims(): array;

    public function getAccessToken(): ?string;

    /**
     * Get all attributes.
     *
     * @return array<string, mixed>
     * @psalm-return TokenSetType
     */
    public function getAttributes(): array;

    public function getCode(): ?string;

    public function getCodeVerifier(): ?string;

    public function getExpiresIn(): ?int;

    public function getIdToken(): ?string;

    public function getRefreshToken(): ?string;

    public function getState(): ?string;

    public function getTokenType(): ?string;

    /**
     * @param array<string, mixed> $claims
     *
     * @return $this
     *
     * @psalm-param TokenSetClaimsType $claims
     */
    public function withClaims(array $claims): self;

    public function withIdToken(string $idToken): self;
}
