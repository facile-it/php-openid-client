<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Client\Metadata;

use JsonSerializable;

interface ClientMetadataInterface extends JsonSerializable
{
    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function get(string $name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    public function getClientId(): string;

    public function getClientSecret(): ?string;

    /**
     * @return string[]
     */
    public function getRedirectUris(): array;

    /**
     * @return string[]
     */
    public function getResponseTypes(): array;

    public function getTokenEndpointAuthMethod(): string;

    public function getAuthorizationSignedResponseAlg(): ?string;

    public function getAuthorizationEncryptedResponseAlg(): ?string;

    public function getAuthorizationEncryptedResponseEnc(): ?string;

    public function getIdTokenSignedResponseAlg(): string;

    public function getIdTokenEncryptedResponseAlg(): ?string;

    public function getIdTokenEncryptedResponseEnc(): ?string;

    public function getUserinfoSignedResponseAlg(): ?string;

    public function getUserinfoEncryptedResponseAlg(): ?string;

    public function getUserinfoEncryptedResponseEnc(): ?string;

    public function getRequestObjectSigningAlg(): ?string;

    public function getRequestObjectEncryptionAlg(): ?string;

    public function getRequestObjectEncryptionEnc(): ?string;

    public function getIntrospectionEndpointAuthMethod(): string;

    public function getRevocationEndpointAuthMethod(): string;

    /**
     * @return array|null
     * @phpstan-return OpenIDJwkSet|null
     */
    public function getJwks(): ?array;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array;

    /**
     * @return array<string, mixed>
     * @phpstan-return OpenIDClientMetadata
     */
    public function toArray(): array;
}
