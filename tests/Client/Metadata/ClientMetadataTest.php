<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Client\Metadata;

use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use Facile\OpenIDClientTest\TestCase;
use function json_decode;
use function json_encode;

/**
 * @internal
 * @coversNothing
 */
final class ClientMetadataTest extends TestCase
{
    public function testDefaults(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->get('require_auth_time'));
        self::assertNull($metadata->get('tls_client_certificate_bound_access_tokens'));
        self::assertNull($metadata->get('response_types'));
        self::assertSame(['code'], $metadata->getResponseTypes());
        self::assertNull($metadata->get('post_logout_redirect_uris'));
        self::assertNull($metadata->get('id_token_signed_response_alg'));
        self::assertSame('RS256', $metadata->getIdTokenSignedResponseAlg());
        self::assertSame('client_secret_basic', $metadata->getTokenEndpointAuthMethod());
    }

    public function testFromClaims(): void
    {
        $metadata = ClientMetadata::fromArray([
            'client_id' => 'foo',
            'redirect_uris' => ['bar'],
        ]);

        self::assertSame('foo', $metadata->getClientId());
        self::assertSame(['bar'], $metadata->getRedirectUris());
    }

    public function testFromClaimsWithNoClientId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ClientMetadata::fromArray([
            'redirect_uris' => ['bar'],
        ]);
    }

    public function testGet(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertSame('foo', $metadata->get('client_id'));
    }

    public function testGetAuthorizationEncryptedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getAuthorizationEncryptedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'authorization_encrypted_response_alg' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getAuthorizationEncryptedResponseAlg());
    }

    public function testGetAuthorizationEncryptedResponseEnc(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getAuthorizationEncryptedResponseEnc());

        $metadata = new ClientMetadata('foo', [
            'authorization_encrypted_response_enc' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getAuthorizationEncryptedResponseEnc());
    }

    public function testGetAuthorizationSignedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getAuthorizationSignedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'authorization_signed_response_alg' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getAuthorizationSignedResponseAlg());
    }

    public function testGetClientId(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertSame('foo', $metadata->getClientId());
    }

    public function testGetClientSecret(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getClientSecret());

        $metadata = new ClientMetadata('foo', [
            'client_secret' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getClientSecret());
    }

    public function testGetIdTokenEncryptedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo');

        self::assertNull($metadata->getIdTokenEncryptedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'id_token_encrypted_response_alg' => 'RSA-OEAP',
        ]);

        self::assertSame('RSA-OEAP', $metadata->getIdTokenEncryptedResponseAlg());
    }

    public function testGetIdTokenEncryptedResponseEnc(): void
    {
        $metadata = new ClientMetadata('foo');

        self::assertNull($metadata->getIdTokenEncryptedResponseEnc());

        $metadata = new ClientMetadata('foo', [
            'id_token_encrypted_response_enc' => 'ALG',
        ]);

        self::assertSame('ALG', $metadata->getIdTokenEncryptedResponseEnc());
    }

    public function testGetIdTokenSignedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo');

        self::assertSame('RS256', $metadata->getIdTokenSignedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'id_token_signed_response_alg' => 'HS256',
        ]);

        self::assertSame('HS256', $metadata->getIdTokenSignedResponseAlg());
    }

    public function testGetIntrospectionEndpointAuthMethod(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertSame($metadata->getTokenEndpointAuthMethod(), $metadata->getIntrospectionEndpointAuthMethod());

        $metadata = new ClientMetadata('foo', [
            'introspection_endpoint_auth_method' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getIntrospectionEndpointAuthMethod());
    }

    public function testGetRedirectUris(): void
    {
        $metadata = new ClientMetadata('foo');

        self::assertSame([], $metadata->getRedirectUris());

        $metadata = new ClientMetadata('foo', [
            'redirect_uris' => ['https://example.com/callback'],
        ]);

        self::assertSame(['https://example.com/callback'], $metadata->getRedirectUris());
    }

    public function testGetRequestObjectEncryptionAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getRequestObjectEncryptionAlg());

        $metadata = new ClientMetadata('foo', [
            'request_object_encryption_alg' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getRequestObjectEncryptionAlg());
    }

    public function testGetRequestObjectEncryptionEnc(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getRequestObjectEncryptionEnc());

        $metadata = new ClientMetadata('foo', [
            'request_object_encryption_enc' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getRequestObjectEncryptionEnc());
    }

    public function testGetRequestObjectSigningAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getRequestObjectSigningAlg());

        $metadata = new ClientMetadata('foo', [
            'request_object_signing_alg' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getRequestObjectSigningAlg());
    }

    public function testGetResponseTypes(): void
    {
        $metadata = new ClientMetadata('foo');

        self::assertSame(['code'], $metadata->getResponseTypes());

        $metadata = new ClientMetadata('foo', [
            'response_types' => ['code', 'id_token'],
        ]);

        self::assertSame(['code', 'id_token'], $metadata->getResponseTypes());
    }

    public function testGetRevocationEndpointAuthMethod(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertSame($metadata->getTokenEndpointAuthMethod(), $metadata->getRevocationEndpointAuthMethod());

        $metadata = new ClientMetadata('foo', [
            'revocation_endpoint_auth_method' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getRevocationEndpointAuthMethod());
    }

    public function testGetTokenEndpointAuthMethod(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertSame('client_secret_basic', $metadata->getTokenEndpointAuthMethod());

        $metadata = new ClientMetadata('foo', [
            'token_endpoint_auth_method' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getTokenEndpointAuthMethod());
    }

    public function testGetUserinfoEncryptedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getUserinfoEncryptedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'userinfo_encrypted_response_alg' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getUserinfoEncryptedResponseAlg());
    }

    public function testGetUserinfoEncryptedResponseEnc(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getUserinfoEncryptedResponseEnc());

        $metadata = new ClientMetadata('foo', [
            'userinfo_encrypted_response_enc' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getUserinfoEncryptedResponseEnc());
    }

    public function testGetUserinfoSignedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertNull($metadata->getUserinfoSignedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'userinfo_signed_response_alg' => 'foo',
        ]);

        self::assertSame('foo', $metadata->getUserinfoSignedResponseAlg());
    }

    public function testHas(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertTrue($metadata->has('client_id'));
        self::assertFalse($metadata->has('foo'));
    }

    public function testJsonSerialize(): void
    {
        $expected = [
            'redirect_uris' => ['bar'],
            'client_id' => 'foo',
        ];
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        self::assertSame($expected, json_decode(json_encode($metadata), true));
    }
}
