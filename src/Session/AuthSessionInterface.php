<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Session;

use JsonSerializable;

interface AuthSessionInterface extends JsonSerializable
{
    public function getState(): ?string;

    public function getNonce(): ?string;

    public function setState(?string $state): void;

    public function setNonce(?string $nonce): void;

    /**
     * @param array<string, mixed> $array
     *
     * @return static
     */
    public static function fromArray(array $array): self;
}
