<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Session;

use JsonSerializable;

interface AuthSessionInterface extends JsonSerializable
{
    public function getState(): ?string;

    public function getNonce(): ?string;

    public function getCodeVerifier(): ?string;

    /**
     * @return array<string, mixed>
     */
    public function getCustoms(): array;

    public function setState(?string $state): void;

    public function setNonce(?string $nonce): void;

    public function setCodeVerifier(?string $codeVerifier): void;

    /**
     * @param array<string, mixed> $customs
     */
    public function setCustoms(array $customs): void;

    /**
     * @param array<string, mixed> $array
     *
     * @return static
     */
    public static function fromArray(array $array): self;
}
