<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata;

use JsonSerializable;

/**
 * @psalm-import-type IssuerMetadataObject from \Facile\JoseVerifier\Psalm\PsalmTypes
 * @psalm-import-type OpenIdDisplayType from \Facile\JoseVerifier\Psalm\PsalmTypes
 * @psalm-import-type OpenIdClaimType from \Facile\JoseVerifier\Psalm\PsalmTypes
 * @psalm-import-type OpenIdResponseType from \Facile\JoseVerifier\Psalm\PsalmTypes
 * @psalm-import-type OpenIdResponseMode from \Facile\JoseVerifier\Psalm\PsalmTypes
 * @psalm-import-type OpenIdGrantType from \Facile\JoseVerifier\Psalm\PsalmTypes
 * @psalm-import-type OpenIdApplicationType from \Facile\JoseVerifier\Psalm\PsalmTypes
 * @psalm-import-type OpenIdSubjectType from \Facile\JoseVerifier\Psalm\PsalmTypes
 * @psalm-import-type OpenIdAuthMethod from \Facile\JoseVerifier\Psalm\PsalmTypes
 */
interface IssuerMetadataInterface extends JsonSerializable
{
    /**
     * @return mixed|null
     */
    public function get(string $name);

    /**
     * @return string[]|null
     * @psalm-return list<non-empty-string>|null
     */
    public function getAcrValuesSupported(): ?array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getAuthorizationEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getAuthorizationEncryptionEncValuesSupported(): array;

    /**
     * @psalm-return non-empty-string
     */
    public function getAuthorizationEndpoint(): string;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getAuthorizationSigningAlgValuesSupported(): array;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getCheckSessionIframe(): ?string;

    /**
     * @return string[]|null
     * @psalm-return list<non-empty-string>|null
     */
    public function getClaimsLocalesSupported(): ?array;

    /**
     * @return string[]|null
     * @psalm-return list<non-empty-string>|null
     */
    public function getClaimsSupported(): ?array;

    /**
     * @return string[]
     * @psalm-return list<OpenIdClaimType>
     */
    public function getClaimTypesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getCodeChallengeMethodsSupported(): ?array;

    /**
     * @return string[]|null
     * @psalm-return list<non-empty-string>|null
     */
    public function getDisplayValuesSupported(): ?array;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getEndSessionIframe(): ?string;

    /**
     * @return string[]
     * @psalm-return list<OpenIdGrantType>
     */
    public function getGrantTypesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getIdTokenEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getIdTokenEncryptionEncValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getIdTokenSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getIntrospectionEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getIntrospectionEncryptionEncValuesSupported(): array;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getIntrospectionEndpoint(): ?string;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getIntrospectionEndpointAuthMethodsSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getIntrospectionEndpointAuthSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getIntrospectionSigningAlgValuesSupported(): array;

    /**
     * @psalm-return non-empty-string
     */
    public function getIssuer(): string;

    /**
     * @psalm-return non-empty-string
     */
    public function getJwksUri(): string;

    /**
     * @return array<string, string>
     */
    public function getMtlsEndpointAliases(): array;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getOpPolicyUri(): ?string;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getOpTosUri(): ?string;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getRegistrationEndpoint(): ?string;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getRequestObjectEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getRequestObjectEncryptionEncValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getRequestObjectSigningAlgValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<OpenIdResponseMode>
     */
    public function getResponseModesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getResponseTypesSupported(): array;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getRevocationEndpoint(): ?string;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getRevocationEndpointAuthMethodsSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getRevocationEndpointAuthSigningAlgValuesSupported(): array;

    /**
     * @return string[]|null
     * @psalm-return list<non-empty-string>|null
     */
    public function getScopesSupported(): ?array;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getServiceDocumentation(): ?string;

    /**
     * @return string[]
     * @psalm-return list<OpenIdSubjectType>
     */
    public function getSubjectTypesSupported(): array;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getTokenEndpoint(): ?string;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getTokenEndpointAuthMethodsSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getTokenEndpointAuthSigningAlgValuesSupported(): array;

    /**
     * @return string[]|null
     * @psalm-return list<non-empty-string>|null
     */
    public function getUiLocalesSupported(): ?array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getUserinfoEncryptionAlgValuesSupported(): array;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getUserinfoEncryptionEncValuesSupported(): array;

    /**
     * @psalm-return non-empty-string|null
     */
    public function getUserinfoEndpoint(): ?string;

    /**
     * @return string[]
     * @psalm-return list<non-empty-string>
     */
    public function getUserinfoSigningAlgValuesSupported(): array;

    public function has(string $name): bool;

    public function isBackchannelLogoutSessionSupported(): bool;

    public function isBackchannelLogoutSupported(): bool;

    public function isClaimsParameterSupported(): bool;

    public function isFrontchannelLogoutSessionSupported(): bool;

    public function isFrontchannelLogoutSupported(): bool;

    public function isRequestParameterSupported(): bool;

    public function isRequestUriParameterSupported(): bool;

    public function isRequireRequestUriRegistration(): bool;

    public function isTlsClientCertificateBoundAccessTokens(): bool;

    /**
     * @return array<string, mixed>
     * @psalm-return IssuerMetadataObject
     */
    public function jsonSerialize(): array;

    /**
     * @return array<string, mixed>
     * @psalm-return IssuerMetadataObject
     */
    public function toArray(): array;
}
