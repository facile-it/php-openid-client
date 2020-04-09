<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata;

use JsonSerializable;

interface IssuerMetadataInterface extends JsonSerializable
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

    /**
     * @return string
     */
    public function getIssuer(): string;

    /**
     * @return string
     */
    public function getAuthorizationEndpoint(): string;

    /**
     * @return string|null
     */
    public function getTokenEndpoint(): ?string;

    /**
     * @return string|null
     */
    public function getUserinfoEndpoint(): ?string;

    /**
     * @return string|null
     */
    public function getRegistrationEndpoint(): ?string;

    /**
     * @return string
     */
    public function getJwksUri(): string;

    /**
     * @return string[]
     */
    public function getScopesSupported(): array;

    /**
     * @return string[]
     */
    public function getResponseTypesSupported(): array;

    /**
     * @return string[]
     */
    public function getResponseModesSupported(): array;

    /**
     * @return string[]
     */
    public function getGrantTypesSupported(): array;

    /**
     * @return string[]
     */
    public function getAcrValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getSubjectTypesSupported(): array;

    /**
     * @return string[]
     */
    public function getDisplayValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getClaimTypesSupported(): array;

    /**
     * @return string[]
     */
    public function getClaimSupported(): array;

    /**
     * @return string|null
     */
    public function getServiceDocumentation(): ?string;

    /**
     * @return string[]|null
     */
    public function getClaimsLocalesSupported(): ?array;

    /**
     * @return string[]|null
     */
    public function getUiLocalesSupported(): ?array;

    /**
     * @return bool
     */
    public function isClaimsParameterSupported(): bool;

    /**
     * @return bool
     */
    public function isRequestParameterSupported(): bool;

    /**
     * @return bool
     */
    public function isRequestUriParameterSupported(): bool;

    /**
     * @return bool
     */
    public function isRequireRequestUriRegistration(): bool;

    /**
     * @return string|null
     */
    public function getOpPolicyUri(): ?string;

    /**
     * @return string|null
     */
    public function getOpTosUri(): ?string;

    /**
     * @return string[]|null
     */
    public function getCodeChallengeMethodsSupported(): ?array;

    /**
     * @return string|null
     */
    public function getSignedMetadata(): ?string;

    /**
     * @return string[]
     */
    public function getTokenEndpointAuthMethodsSupported(): array;

    /**
     * @return string[]
     */
    public function getTokenEndpointAuthSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getIdTokenSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getIdTokenEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getIdTokenEncryptionEncValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getUserinfoSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getUserinfoEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getUserinfoEncryptionEncValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getAuthorizationSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getAuthorizationEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getAuthorizationEncryptionEncValuesSupported(): array;

    /**
     * @return string|null
     */
    public function getIntrospectionEndpoint(): ?string;

    /**
     * @return string[]
     */
    public function getIntrospectionEndpointAuthMethodsSupported(): array;

    /**
     * @return string[]
     */
    public function getIntrospectionEndpointAuthSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getIntrospectionSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getIntrospectionEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getIntrospectionEncryptionEncValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getRequestObjectSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getRequestObjectEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     */
    public function getRequestObjectEncryptionEncValuesSupported(): array;

    /**
     * @return string|null
     */
    public function getRevocationEndpoint(): ?string;

    /**
     * @return string[]
     */
    public function getRevocationEndpointAuthMethodsSupported(): array;

    /**
     * @return string[]
     */
    public function getRevocationEndpointAuthSigningAlgValuesSupported(): array;

    /**
     * @return string|null
     */
    public function getCheckSessionIframe(): ?string;

    /**
     * @return string|null
     */
    public function getEndSessionIframe(): ?string;

    /**
     * @return bool
     */
    public function isFrontchannelLogoutSupported(): bool;

    /**
     * @return bool
     */
    public function isFrontchannelLogoutSessionSupported(): bool;

    /**
     * @return bool
     */
    public function isBackchannelLogoutSupported(): bool;

    /**
     * @return bool
     */
    public function isBackchannelLogoutSessionSupported(): bool;

    /**
     * @return bool
     */
    public function isTlsClientCertificateBoundAccessTokens(): bool;

    /**
     * @return array<string, string>
     */
    public function getMtlsEndpointAliases(): array;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array;

    /**
     * @return array<string, mixed>
     * @phpstan-return OpenIDIssuerMetadata
     */
    public function toArray(): array;
}
