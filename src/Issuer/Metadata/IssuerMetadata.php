<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata;

use function array_diff;
use function array_filter;
use const ARRAY_FILTER_USE_BOTH;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function count;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use function implode;

final class IssuerMetadata implements IssuerMetadataInterface
{
    /**
     * @var array<string, mixed>
     * @phpstan-var OpenIDIssuerMetadata
     */
    private $metadata;

    /** @var string[] */
    private static $requiredKeys = [
        'issuer',
        'authorization_endpoint',
        'jwks_uri',
    ];

    /** @var array<string, mixed> */
    private static $defaults = [
        'scopes_supported' => ['openid'],
        'response_types_supported' => ['code', 'id_token', 'token id_token'],
        'response_modes_supported' => ['query', 'fragment'],
        'grant_types_supported' => ['authorization_code', 'implicit'],
        'acr_values_supported' => [],
        'subject_types_supported' => ['public'],
        'display_values_supported' => [],
        'claim_types_supported' => ['normal'],
        'claim_supported' => [],

        'claims_parameter_supported' => false,
        'request_parameter_supported' => false,
        'request_uri_parameter_supported' => true,
        'require_request_uri_registration' => false,
        'token_endpoint_auth_methods_supported' => ['client_secret_basic'],
        'token_endpoint_auth_signing_alg_values_supported' => ['RS256'],

        'id_token_signing_alg_values_supported' => ['RS256'],
        'id_token_encryption_alg_values_supported' => [],
        'id_token_encryption_enc_values_supported' => [],

        'userinfo_signing_alg_values_supported' => ['RS256'],
        'userinfo_encryption_alg_values_supported' => [],
        'userinfo_encryption_enc_values_supported' => [],

        'authorization_signing_alg_values_supported' => ['RS256'],
        'authorization_encryption_alg_values_supported' => [],
        'authorization_encryption_enc_values_supported' => [],

        'introspection_endpoint_auth_methods_supported' => ['client_secret_basic'],
        'introspection_endpoint_auth_signing_alg_values_supported' => ['RS256'],

        'introspection_signing_alg_values_supported' => ['RS256'],
        'introspection_encryption_alg_values_supported' => [],
        'introspection_encryption_enc_values_supported' => [],

        'request_object_signing_alg_values_supported' => ['RS256'],
        'request_object_encryption_alg_values_supported' => [],
        'request_object_encryption_enc_values_supported' => [],

        'revocation_endpoint_auth_methods_supported' => [],
        'revocation_signing_alg_values_supported' => ['RS256'],

        'frontchannel_logout_supported' => false,
        'frontchannel_logout_session_supported' => false,
        'backchannel_logout_supported' => false,
        'backchannel_logout_session_supported' => false,
        'tls_client_certificate_bound_access_tokens' => false,
        'mtls_endpoint_aliases' => [],
    ];

    /**
     * IssuerMetadata constructor.
     *
     * @param string $issuer
     * @param string $authorizationEndpoint
     * @param string $jwksUri
     * @param array<string, mixed> $claims
     */
    public function __construct(
        string $issuer,
        string $authorizationEndpoint,
        string $jwksUri,
        array $claims = []
    ) {
        $requiredClaims = [
            'issuer' => $issuer,
            'authorization_endpoint' => $authorizationEndpoint,
            'jwks_uri' => $jwksUri,
        ];

        $defaults = self::$defaults;

        $this->metadata = array_merge($defaults, $claims, $requiredClaims);
    }

    /**
     * @param array<string, mixed> $claims
     *
     * @return static
     *
     * @phpstan-param OpenIDIssuerMetadata $claims
     */
    public static function fromArray(array $claims): self
    {
        $missingKeys = array_diff(self::$requiredKeys, array_keys($claims));
        if (0 !== count($missingKeys)) {
            throw new InvalidArgumentException('Invalid issuer metadata. Missing keys: ' . implode(', ', $missingKeys));
        }

        return new static(
            $claims['issuer'],
            $claims['authorization_endpoint'],
            $claims['jwks_uri'],
            $claims
        );
    }

    /**
     * @return string
     */
    public function getIssuer(): string
    {
        return $this->metadata['issuer'];
    }

    /**
     * @return string
     */
    public function getAuthorizationEndpoint(): string
    {
        return $this->metadata['authorization_endpoint'];
    }

    /**
     * @return string|null
     */
    public function getTokenEndpoint(): ?string
    {
        return $this->metadata['token_endpoint'];
    }

    /**
     * @return string|null
     */
    public function getUserinfoEndpoint(): ?string
    {
        return $this->metadata['userinfo_endpoint'];
    }

    /**
     * @return string|null
     */
    public function getRegistrationEndpoint(): ?string
    {
        return $this->metadata['registration_endpoint'];
    }

    /**
     * @return string
     */
    public function getJwksUri(): string
    {
        return $this->metadata['jwks_uri'];
    }

    /**
     * @return string[]
     */
    public function getScopesSupported(): array
    {
        return $this->metadata['scopes_supported'];
    }

    /**
     * @return string[]
     */
    public function getResponseTypesSupported(): array
    {
        return $this->metadata['response_types_supported'];
    }

    /**
     * @return string[]
     */
    public function getResponseModesSupported(): array
    {
        return $this->metadata['response_modes_supported'];
    }

    /**
     * @return string[]
     */
    public function getGrantTypesSupported(): array
    {
        return $this->metadata['grant_types_supported'];
    }

    /**
     * @return string[]
     */
    public function getAcrValuesSupported(): array
    {
        return $this->metadata['acr_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getSubjectTypesSupported(): array
    {
        return $this->metadata['subject_types_supported'];
    }

    /**
     * @return string[]
     */
    public function getDisplayValuesSupported(): array
    {
        return $this->metadata['display_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getClaimTypesSupported(): array
    {
        return $this->metadata['claim_types_supported'];
    }

    /**
     * @return string[]
     */
    public function getClaimSupported(): array
    {
        return $this->metadata['claim_supported'];
    }

    /**
     * @return string|null
     */
    public function getServiceDocumentation(): ?string
    {
        return $this->metadata['service_documentation'];
    }

    /**
     * @return string[]|null
     */
    public function getClaimsLocalesSupported(): ?array
    {
        return $this->metadata['claims_locales_supported'];
    }

    /**
     * @return string[]|null
     */
    public function getUiLocalesSupported(): ?array
    {
        return $this->metadata['ui_locales_supported'];
    }

    /**
     * @return bool
     */
    public function isClaimsParameterSupported(): bool
    {
        return $this->metadata['claims_parameter_supported'];
    }

    /**
     * @return bool
     */
    public function isRequestParameterSupported(): bool
    {
        return $this->metadata['request_parameter_supported'];
    }

    /**
     * @return bool
     */
    public function isRequestUriParameterSupported(): bool
    {
        return $this->metadata['request_uri_parameter_supported'];
    }

    /**
     * @return bool
     */
    public function isRequireRequestUriRegistration(): bool
    {
        return $this->metadata['require_request_uri_registration'];
    }

    /**
     * @return string|null
     */
    public function getOpPolicyUri(): ?string
    {
        return $this->metadata['op_policy_uri'];
    }

    /**
     * @return string|null
     */
    public function getOpTosUri(): ?string
    {
        return $this->metadata['op_tos_uri'];
    }

    /**
     * @return string[]|null
     */
    public function getCodeChallengeMethodsSupported(): ?array
    {
        return $this->metadata['code_challenge_methods_supported'];
    }

    /**
     * @return string|null
     */
    public function getSignedMetadata(): ?string
    {
        return $this->metadata['signed_metadata'];
    }

    /**
     * @return string[]
     */
    public function getTokenEndpointAuthMethodsSupported(): array
    {
        return $this->metadata['token_endpoint_auth_methods_supported'];
    }

    /**
     * @return string[]
     */
    public function getTokenEndpointAuthSigningAlgValuesSupported(): array
    {
        return $this->metadata['token_endpoint_auth_signing_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getIdTokenSigningAlgValuesSupported(): array
    {
        return $this->metadata['id_token_signing_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getIdTokenEncryptionAlgValuesSupported(): array
    {
        return $this->metadata['id_token_encryption_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getIdTokenEncryptionEncValuesSupported(): array
    {
        return $this->metadata['id_token_encryption_enc_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getUserinfoSigningAlgValuesSupported(): array
    {
        return $this->metadata['userinfo_signing_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getUserinfoEncryptionAlgValuesSupported(): array
    {
        return $this->metadata['userinfo_encryption_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getUserinfoEncryptionEncValuesSupported(): array
    {
        return $this->metadata['userinfo_encryption_enc_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getAuthorizationSigningAlgValuesSupported(): array
    {
        return $this->metadata['authorization_signing_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getAuthorizationEncryptionAlgValuesSupported(): array
    {
        return $this->metadata['authorization_encryption_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getAuthorizationEncryptionEncValuesSupported(): array
    {
        return $this->metadata['authorization_encryption_enc_values_supported'];
    }

    /**
     * @return string|null
     */
    public function getIntrospectionEndpoint(): ?string
    {
        return $this->metadata['introspection_endpoint'];
    }

    /**
     * @return string[]
     */
    public function getIntrospectionEndpointAuthMethodsSupported(): array
    {
        return $this->metadata['introspection_endpoint_auth_methods_supported'];
    }

    /**
     * @return string[]
     */
    public function getIntrospectionEndpointAuthSigningAlgValuesSupported(): array
    {
        return $this->metadata['introspection_endpoint_auth_signing_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getIntrospectionSigningAlgValuesSupported(): array
    {
        return $this->metadata['introspection_signing_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getIntrospectionEncryptionAlgValuesSupported(): array
    {
        return $this->metadata['introspection_encryption_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getIntrospectionEncryptionEncValuesSupported(): array
    {
        return $this->metadata['introspection_encryption_enc_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getRequestObjectSigningAlgValuesSupported(): array
    {
        return $this->metadata['request_object_signing_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getRequestObjectEncryptionAlgValuesSupported(): array
    {
        return $this->metadata['request_object_encryption_alg_values_supported'];
    }

    /**
     * @return string[]
     */
    public function getRequestObjectEncryptionEncValuesSupported(): array
    {
        return $this->metadata['request_object_encryption_enc_values_supported'];
    }

    /**
     * @return string|null
     */
    public function getRevocationEndpoint(): ?string
    {
        return $this->metadata['revocation_endpoint'];
    }

    /**
     * @return string[]
     */
    public function getRevocationEndpointAuthMethodsSupported(): array
    {
        return $this->metadata['revocation_endpoint_auth_methods_supported'];
    }

    /**
     * @return string[]
     */
    public function getRevocationEndpointAuthSigningAlgValuesSupported(): array
    {
        return $this->metadata['revocation_endpoint_auth_signing_alg_values_supported'];
    }

    /**
     * @return string|null
     */
    public function getCheckSessionIframe(): ?string
    {
        return $this->metadata['check_session_iframe'];
    }

    /**
     * @return string|null
     */
    public function getEndSessionIframe(): ?string
    {
        return $this->metadata['end_session_iframe'];
    }

    /**
     * @return bool
     */
    public function isFrontchannelLogoutSupported(): bool
    {
        return $this->metadata['frontchannel_logout_supported'];
    }

    /**
     * @return bool
     */
    public function isFrontchannelLogoutSessionSupported(): bool
    {
        return $this->metadata['frontchannel_logout_session_supported'];
    }

    /**
     * @return bool
     */
    public function isBackchannelLogoutSupported(): bool
    {
        return $this->metadata['backchannel_logout_supported'];
    }

    /**
     * @return bool
     */
    public function isBackchannelLogoutSessionSupported(): bool
    {
        return $this->metadata['backchannel_logout_session_supported'];
    }

    /**
     * @return bool
     */
    public function isTlsClientCertificateBoundAccessTokens(): bool
    {
        return $this->metadata['tls_client_certificate_bound_access_tokens'];
    }

    /**
     * @return array<string, string>
     */
    public function getMtlsEndpointAliases(): array
    {
        return $this->metadata['mtls_endpoint_aliases'];
    }

    /**
     * @return array<string, mixed>
     */
    private function getFilteredClaims(): array
    {
        return array_filter($this->metadata, static function ($value, string $key): bool {
            return array_key_exists($key, self::$requiredKeys)
                || $value !== (self::$defaults[$key] ?? null);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->getFilteredClaims();
    }

    /**
     * @return array<string, mixed>
     * @phpstan-return OpenIDIssuerMetadata
     */
    public function toArray(): array
    {
        return $this->getFilteredClaims();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->metadata);
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function get(string $name)
    {
        return $this->metadata[$name] ?? null;
    }
}
