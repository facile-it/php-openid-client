<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Session;

use JsonSerializable;

// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @psalm-type AuthSessionType = array{state?: string, nonce?: string, code_verifier?: string, customs?: array<string, mixed>}
 */
interface AuthSessionInterface extends JsonSerializable
{
    /**
     * @param array<string, mixed> $array
     *
     * @return static
     *
     * @psalm-param AuthSessionType $array
     */
    public static function fromArray(array $array): self;

    public function getCodeVerifier(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function getCustoms(): array;

    public function getNonce(): ?string;

    public function getState(): ?string;

    /**
     * @return array<string, mixed>
     * @psalm-return AuthSessionType
     */
    public function jsonSerialize(): array;

    public function setCodeVerifier(?string $codeVerifier): void;

    /**
     * @param array<string, mixed> $customs
     */
    public function setCustoms(array $customs): void;

    public function setNonce(?string $nonce): void;

    public function setState(?string $state): void;
}
