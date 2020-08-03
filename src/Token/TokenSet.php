<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Token;

use function array_key_exists;
use JsonSerializable;

final class TokenSet implements TokenSetInterface, JsonSerializable
{
    /** @var array<string, mixed> */
    private $attributes = [];

    /** @var array<string, mixed> */
    private $claims;

    /**
     * @param array<string, mixed> $data
     *
     * @return TokenSetInterface
     */
    public static function fromParams(array $data): TokenSetInterface
    {
        $token = new static();

        if (array_key_exists('claims', $data)) {
            $token->claims = $data['claims'];
            unset($data['claims']);
        }

        $token->attributes = $data;

        return $token;
    }

    /**
     * Get all attributes
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->attributes['code'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->attributes['state'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getTokenType(): ?string
    {
        return $this->attributes['token_type'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->attributes['access_token'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getIdToken(): ?string
    {
        return $this->attributes['id_token'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->attributes['refresh_token'] ?? null;
    }

    /**
     * @return int|null
     */
    public function getExpiresIn(): ?int
    {
        return array_key_exists('expires_in', $this->attributes) ? (int) $this->attributes['expires_in'] : null;
    }

    /**
     * @return string|null
     */
    public function getCodeVerifier(): ?string
    {
        return $this->attributes['code_verifier'] ?? null;
    }

    public function withIdToken(string $idToken): TokenSetInterface
    {
        $clone = clone $this;
        $clone->attributes['id_token'] = $idToken;

        return $clone;
    }

    public function withClaims(array $claims): TokenSetInterface
    {
        $clone = clone $this;
        $clone->claims = $claims;

        return $clone;
    }

    /**
     * @return array<string, mixed>
     * @phpstan-return array{code?: string, state?: string, token_type?: string, access_token?: string, id_token?: string, refresh_token?: string, expires_in?: int, code_verifier?: string}
     */
    public function jsonSerialize(): array
    {
        return $this->attributes;
    }

    /**
     * @return array<string, mixed>
     */
    public function claims(): array
    {
        return $this->claims;
    }
}
