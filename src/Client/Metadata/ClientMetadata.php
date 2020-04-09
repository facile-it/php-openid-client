<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Client\Metadata;

use function array_diff;
use function array_filter;
use const ARRAY_FILTER_USE_BOTH;
use function array_key_exists;
use function array_keys;
use function array_merge;
use function count;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use function implode;

final class ClientMetadata implements ClientMetadataInterface
{
    /** @var array<string, mixed> */
    private $metadata;

    /** @var string[] */
    private static $requiredKeys = [
        'client_id',
    ];

    /** @var array<string, mixed> */
    private static $defaults = [
        'client_id' => null,
        'redirect_uris' => [],
        'client_secret' => null,
        'jwks' => null,
        'jwks_uri' => null,
        'id_token_signed_response_alg' => 'RS256',
        'id_token_encrypted_response_alg' => null,
        'id_token_encrypted_response_enc' => null,
        'userinfo_signed_response_alg' => null,
        'userinfo_encrypted_response_alg' => null,
        'userinfo_encrypted_response_enc' => null,
        'response_types' => ['code'],
        'post_logout_redirect_uris' => [],
        'require_auth_time' => false,
        'request_object_signing_alg' => null,
        'request_object_encryption_alg' => null,
        'request_object_encryption_enc' => null,
        'token_endpoint_auth_method' => 'client_secret_basic',
        //'introspection_endpoint_auth_method' => 'client_secret_basic',
        //'revocation_endpoint_auth_method' => 'client_secret_basic',
        'token_endpoint_auth_signing_alg' => null,
        'introspection_endpoint_auth_signing_alg' => null,
        'revocation_endpoint_auth_signing_alg' => null,
        'tls_client_certificate_bound_access_tokens' => false,
    ];

    /**
     * IssuerMetadata constructor.
     *
     * @param string $clientId
     * @param array<string, mixed> $claims
     */
    public function __construct(string $clientId, $claims = [])
    {
        $requiredClaims = [
            'client_id' => $clientId,
        ];

        $defaults = self::$defaults;

        $this->metadata = array_merge($defaults, $claims, $requiredClaims);
    }

    /**
     * @param array<string, mixed> $claims
     *
     * @return static
     *
     * @phpstan-param OpenIDClientMetadata $claims
     */
    public static function fromArray(array $claims): self
    {
        $missingKeys = array_diff(self::$requiredKeys, array_keys($claims));
        if (0 !== count($missingKeys)) {
            throw new InvalidArgumentException(
                'Invalid client metadata. Missing keys: ' . implode(', ', $missingKeys)
            );
        }

        return new static($claims['client_id'], $claims);
    }

    public function getClientId(): string
    {
        return $this->metadata['client_id'];
    }

    public function getClientSecret(): ?string
    {
        return $this->metadata['client_secret'] ?? null;
    }

    public function getRedirectUris(): array
    {
        return $this->metadata['redirect_uris'] ?? [];
    }

    public function getResponseTypes(): array
    {
        return $this->metadata['response_types'] ?? ['code'];
    }

    public function getTokenEndpointAuthMethod(): string
    {
        return $this->metadata['token_endpoint_auth_method'];
    }

    public function getAuthorizationSignedResponseAlg(): ?string
    {
        return $this->metadata['authorization_signed_response_alg'] ?? null;
    }

    public function getAuthorizationEncryptedResponseAlg(): ?string
    {
        return $this->metadata['authorization_encrypted_response_alg'] ?? null;
    }

    public function getAuthorizationEncryptedResponseEnc(): ?string
    {
        return $this->metadata['authorization_encrypted_response_enc'] ?? null;
    }

    public function getIdTokenSignedResponseAlg(): string
    {
        return $this->metadata['id_token_signed_response_alg'];
    }

    public function getIdTokenEncryptedResponseAlg(): ?string
    {
        return $this->metadata['id_token_encrypted_response_alg'] ?? null;
    }

    public function getIdTokenEncryptedResponseEnc(): ?string
    {
        return $this->metadata['id_token_encrypted_response_enc'] ?? null;
    }

    public function getUserinfoSignedResponseAlg(): ?string
    {
        return $this->metadata['userinfo_signed_response_alg'] ?? null;
    }

    public function getUserinfoEncryptedResponseAlg(): ?string
    {
        return $this->metadata['userinfo_encrypted_response_alg'] ?? null;
    }

    public function getUserinfoEncryptedResponseEnc(): ?string
    {
        return $this->metadata['userinfo_encrypted_response_enc'] ?? null;
    }

    public function getRequestObjectSigningAlg(): ?string
    {
        return $this->metadata['request_object_signing_alg'] ?? null;
    }

    public function getRequestObjectEncryptionAlg(): ?string
    {
        return $this->metadata['request_object_encryption_alg'] ?? null;
    }

    public function getRequestObjectEncryptionEnc(): ?string
    {
        return $this->metadata['request_object_encryption_enc'] ?? null;
    }

    public function getIntrospectionEndpointAuthMethod(): string
    {
        return $this->metadata['introspection_endpoint_auth_method'] ?? $this->getTokenEndpointAuthMethod();
    }

    public function getRevocationEndpointAuthMethod(): string
    {
        return $this->metadata['revocation_endpoint_auth_method'] ?? $this->getTokenEndpointAuthMethod();
    }

    /**
     * @return array|null
     * @phpstan-return array{keys: array<int, array<string, mixed>>}
     */
    public function getJwks(): ?array
    {
        return $this->metadata['jwks'] ?? null;
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
