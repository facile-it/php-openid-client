<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Token;

interface TokenSetInterface
{
    /**
     * Get all attributes
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array;

    public function getTokenType(): ?string;

    public function getAccessToken(): ?string;

    public function getIdToken(): ?string;

    public function getRefreshToken(): ?string;

    public function getExpiresIn(): ?int;

    public function getCodeVerifier(): ?string;

    public function getCode(): ?string;

    public function getState(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function claims(): array;

    public function withIdToken(string $idToken): self;

    /**
     * @param array<string, mixed> $claims
     *
     * @return $this
     */
    public function withClaims(array $claims): self;
}
