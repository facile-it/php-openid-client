<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Issuer\Metadata;

use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadata;
use function json_decode;
use function json_encode;
use PHPUnit\Framework\TestCase;

class IssuerMetadataTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $metadata = new IssuerMetadata(
            'foo-issuer',
            'foo-endpoint',
            'foo-jwks'
        );

        $expected = [
            'issuer' => 'foo-issuer',
            'authorization_endpoint' => 'foo-endpoint',
            'jwks_uri' => 'foo-jwks',
        ];

        static::assertSame($expected, json_decode(json_encode($metadata), true));
    }

    public function testFromArray(): void
    {
        $metadata = IssuerMetadata::fromArray([
            'issuer' => 'foo',
            'authorization_endpoint' => 'foo-endpoint',
            'jwks_uri' => 'foo-jwks',
        ]);

        static::assertInstanceOf(IssuerMetadata::class, $metadata);
        static::assertSame('foo', $metadata->getIssuer());
        static::assertSame('foo-endpoint', $metadata->getAuthorizationEndpoint());
        static::assertSame('foo-jwks', $metadata->getJwksUri());
    }

    public function testGet(): void
    {
        $metadata = new IssuerMetadata(
            'foo-issuer',
            'foo-endpoint',
            'foo-jwks',
            [
                'foo' => 'bar',
            ]
        );

        static::assertSame('foo-issuer', $metadata->get('issuer'));
        static::assertSame('bar', $metadata->get('foo'));
        static::assertNull($metadata->get('foo2'));
    }

    public function testHas(): void
    {
        $metadata = new IssuerMetadata(
            'foo-issuer',
            'foo-endpoint',
            'foo-jwks',
            [
                'foo' => 'bar',
            ]
        );

        static::assertTrue($metadata->has('issuer'));
        static::assertTrue($metadata->has('foo'));
        static::assertFalse($metadata->has('foo2'));
    }

    /**
     * @dataProvider getClaimGetterProvider
     *
     * @param string $claim
     * @param string $methodName
     * @param mixed $value
     */
    public function testGetters(string $claim, string $methodName, $value): void
    {
        $metadata = new IssuerMetadata(
            'foo',
            'foo',
            'foo',
            [
                $claim => $value,
            ]
        );

        /** @var callable $callable */
        $callable = [$metadata, $methodName];
        static::assertSame($value, $callable());
    }

    public function getClaimGetterProvider(): array
    {
        return [
            ['issuer', 'getIssuer', 'foo'],
            ['authorization_endpoint', 'getAuthorizationEndpoint', 'foo'],
            ['token_endpoint', 'getTokenEndpoint', 'foo'],
            ['userinfo_endpoint', 'getUserinfoEndpoint', 'foo'],
            ['registration_endpoint', 'getRegistrationEndpoint', 'foo'],
            ['jwks_uri', 'getJwksUri', 'foo'],
            ['scopes_supported', 'getScopesSupported', ['foo', 'bar']],
            ['response_types_supported', 'getResponseTypesSupported', ['foo', 'bar']],
            ['response_modes_supported', 'getResponseModesSupported', ['foo', 'bar']],
            ['grant_types_supported', 'getGrantTypesSupported', ['foo', 'bar']],
            ['acr_values_supported', 'getAcrValuesSupported', ['foo', 'bar']],
            ['subject_types_supported', 'getSubjectTypesSupported', ['foo', 'bar']],
            ['display_values_supported', 'getDisplayValuesSupported', ['foo', 'bar']],
            ['claim_types_supported', 'getClaimTypesSupported', ['foo', 'bar']],
            ['claim_supported', 'getClaimSupported', ['foo', 'bar']],
            ['service_documentation', 'getServiceDocumentation', 'foo'],
            ['claims_locales_supported', 'getClaimsLocalesSupported', ['foo', 'bar']],
            ['ui_locales_supported', 'getUiLocalesSupported', ['foo', 'bar']],
            ['claims_parameter_supported', 'isClaimsParameterSupported', true],
            ['request_parameter_supported', 'isRequestParameterSupported', true],
            ['request_uri_parameter_supported', 'isRequestUriParameterSupported', true],
            ['require_request_uri_registration', 'isRequireRequestUriRegistration', true],
            ['claims_parameter_supported', 'isClaimsParameterSupported', false],
            ['request_parameter_supported', 'isRequestParameterSupported', false],
            ['request_uri_parameter_supported', 'isRequestUriParameterSupported', false],
            ['require_request_uri_registration', 'isRequireRequestUriRegistration', false],
            ['op_policy_uri', 'getOpPolicyUri', 'foo'],
            ['op_tos_uri', 'getOpTosUri', 'foo'],
            ['code_challenge_methods_supported', 'getCodeChallengeMethodsSupported', ['foo', 'bar']],
            ['signed_metadata', 'getSignedMetadata', 'foo'],
            ['token_endpoint_auth_methods_supported', 'getTokenEndpointAuthMethodsSupported', ['foo', 'bar']],
            ['token_endpoint_auth_signing_alg_values_supported', 'getTokenEndpointAuthSigningAlgValuesSupported', ['foo', 'bar']],

            ['id_token_signing_alg_values_supported', 'getIdTokenSigningAlgValuesSupported', ['foo', 'bar']],
            ['id_token_encryption_alg_values_supported', 'getIdTokenEncryptionAlgValuesSupported', ['foo', 'bar']],
            ['id_token_encryption_enc_values_supported', 'getIdTokenEncryptionEncValuesSupported', ['foo', 'bar']],
            ['userinfo_signing_alg_values_supported', 'getUserinfoSigningAlgValuesSupported', ['foo', 'bar']],
            ['userinfo_encryption_alg_values_supported', 'getUserinfoEncryptionAlgValuesSupported', ['foo', 'bar']],
            ['userinfo_encryption_enc_values_supported', 'getUserinfoEncryptionEncValuesSupported', ['foo', 'bar']],
            ['authorization_signing_alg_values_supported', 'getAuthorizationSigningAlgValuesSupported', ['foo', 'bar']],
            ['authorization_encryption_alg_values_supported', 'getAuthorizationEncryptionAlgValuesSupported', ['foo', 'bar']],
            ['authorization_encryption_enc_values_supported', 'getAuthorizationEncryptionEncValuesSupported', ['foo', 'bar']],
            ['introspection_endpoint', 'getIntrospectionEndpoint', 'foo'],
            ['introspection_endpoint_auth_methods_supported', 'getIntrospectionEndpointAuthMethodsSupported', ['foo', 'bar']],
            ['introspection_endpoint_auth_signing_alg_values_supported', 'getIntrospectionEndpointAuthSigningAlgValuesSupported', ['foo', 'bar']],

            ['introspection_signing_alg_values_supported', 'getIntrospectionSigningAlgValuesSupported', ['foo', 'bar']],
            ['introspection_encryption_alg_values_supported', 'getIntrospectionEncryptionAlgValuesSupported', ['foo', 'bar']],
            ['introspection_encryption_enc_values_supported', 'getIntrospectionEncryptionEncValuesSupported', ['foo', 'bar']],
            ['request_object_signing_alg_values_supported', 'getRequestObjectSigningAlgValuesSupported', ['foo', 'bar']],
            ['request_object_encryption_alg_values_supported', 'getRequestObjectEncryptionAlgValuesSupported', ['foo', 'bar']],
            ['request_object_encryption_enc_values_supported', 'getRequestObjectEncryptionEncValuesSupported', ['foo', 'bar']],
            ['revocation_endpoint', 'getRevocationEndpoint', 'foo'],
            ['revocation_endpoint_auth_methods_supported', 'getRevocationEndpointAuthMethodsSupported', ['foo', 'bar']],
            ['revocation_endpoint_auth_signing_alg_values_supported', 'getRevocationEndpointAuthSigningAlgValuesSupported', ['foo', 'bar']],

            ['check_session_iframe', 'getCheckSessionIframe', 'foo'],
            ['end_session_iframe', 'getEndSessionIframe', 'foo'],
            ['frontchannel_logout_supported', 'isFrontchannelLogoutSupported', true],
            ['frontchannel_logout_session_supported', 'isFrontchannelLogoutSessionSupported', true],
            ['backchannel_logout_supported', 'isBackchannelLogoutSupported', true],
            ['backchannel_logout_session_supported', 'isBackchannelLogoutSessionSupported', true],
            ['tls_client_certificate_bound_access_tokens', 'isTlsClientCertificateBoundAccessTokens', true],
            ['frontchannel_logout_supported', 'isFrontchannelLogoutSupported', false],
            ['frontchannel_logout_session_supported', 'isFrontchannelLogoutSessionSupported', false],
            ['backchannel_logout_supported', 'isBackchannelLogoutSupported', false],
            ['backchannel_logout_session_supported', 'isBackchannelLogoutSessionSupported', false],
            ['tls_client_certificate_bound_access_tokens', 'isTlsClientCertificateBoundAccessTokens', false],
            ['mtls_endpoint_aliases', 'getMtlsEndpointAliases', ['foo' => 'foo', 'bar' => 'bar']],
        ];
    }
}
