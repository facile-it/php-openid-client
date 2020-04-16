<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Client\Metadata;

use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use function json_decode;
use function json_encode;
use PHPUnit\Framework\TestCase;

class ClientMetadataTest extends TestCase
{
    public function testFromClaims(): void
    {
        $metadata = ClientMetadata::fromArray([
            'client_id' => 'foo',
            'redirect_uris' => ['bar'],
        ]);

        static::assertSame('foo', $metadata->getClientId());
        static::assertSame(['bar'], $metadata->getRedirectUris());
    }

    public function testFromClaimsWithNoClientId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ClientMetadata::fromArray([
            'redirect_uris' => ['bar'],
        ]);
    }

    public function testGetClientId(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertSame('foo', $metadata->getClientId());
    }

    public function testGetRedirectUris(): void
    {
        $metadata = new ClientMetadata('foo');

        static::assertSame([], $metadata->getRedirectUris());

        $metadata = new ClientMetadata('foo', [
            'redirect_uris' => ['https://example.com/callback'],
        ]);

        static::assertSame(['https://example.com/callback'], $metadata->getRedirectUris());
    }

    public function testGetResponseTypes(): void
    {
        $metadata = new ClientMetadata('foo');

        static::assertSame(['code'], $metadata->getResponseTypes());

        $metadata = new ClientMetadata('foo', [
            'response_types' => ['code', 'id_token'],
        ]);

        static::assertSame(['code', 'id_token'], $metadata->getResponseTypes());
    }

    public function testGetIdTokenSignedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo');

        static::assertSame('RS256', $metadata->getIdTokenSignedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'id_token_signed_response_alg' => 'HS256',
        ]);

        static::assertSame('HS256', $metadata->getIdTokenSignedResponseAlg());
    }

    public function testGetIdTokenEncryptedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo');

        static::assertNull($metadata->getIdTokenEncryptedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'id_token_encrypted_response_alg' => 'RSA-OEAP',
        ]);

        static::assertSame('RSA-OEAP', $metadata->getIdTokenEncryptedResponseAlg());
    }

    public function testGetIdTokenEncryptedResponseEnc(): void
    {
        $metadata = new ClientMetadata('foo');

        static::assertNull($metadata->getIdTokenEncryptedResponseEnc());

        $metadata = new ClientMetadata('foo', [
            'id_token_encrypted_response_enc' => 'ALG',
        ]);

        static::assertSame('ALG', $metadata->getIdTokenEncryptedResponseEnc());
    }

    public function testGetUserinfoEncryptedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getUserinfoEncryptedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'userinfo_encrypted_response_alg' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getUserinfoEncryptedResponseAlg());
    }

    public function testGetRevocationEndpointAuthMethod(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertSame($metadata->getTokenEndpointAuthMethod(), $metadata->getRevocationEndpointAuthMethod());

        $metadata = new ClientMetadata('foo', [
            'revocation_endpoint_auth_method' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getRevocationEndpointAuthMethod());
    }

    public function testGetClientSecret(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getClientSecret());

        $metadata = new ClientMetadata('foo', [
            'client_secret' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getClientSecret());
    }

    public function testGetAuthorizationEncryptedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getAuthorizationEncryptedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'authorization_encrypted_response_alg' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getAuthorizationEncryptedResponseAlg());
    }

    public function testGetUserinfoSignedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getUserinfoSignedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'userinfo_signed_response_alg' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getUserinfoSignedResponseAlg());
    }

    public function testGetIntrospectionEndpointAuthMethod(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertSame($metadata->getTokenEndpointAuthMethod(), $metadata->getIntrospectionEndpointAuthMethod());

        $metadata = new ClientMetadata('foo', [
            'introspection_endpoint_auth_method' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getIntrospectionEndpointAuthMethod());
    }

    public function testGetUserinfoEncryptedResponseEnc(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getUserinfoEncryptedResponseEnc());

        $metadata = new ClientMetadata('foo', [
            'userinfo_encrypted_response_enc' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getUserinfoEncryptedResponseEnc());
    }

    public function testGetAuthorizationEncryptedResponseEnc(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getAuthorizationEncryptedResponseEnc());

        $metadata = new ClientMetadata('foo', [
            'authorization_encrypted_response_enc' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getAuthorizationEncryptedResponseEnc());
    }

    public function testGetRequestObjectSigningAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getRequestObjectSigningAlg());

        $metadata = new ClientMetadata('foo', [
            'request_object_signing_alg' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getRequestObjectSigningAlg());
    }

    public function testGetRequestObjectEncryptionAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getRequestObjectEncryptionAlg());

        $metadata = new ClientMetadata('foo', [
            'request_object_encryption_alg' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getRequestObjectEncryptionAlg());
    }

    public function testGetRequestObjectEncryptionEnc(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getRequestObjectEncryptionEnc());

        $metadata = new ClientMetadata('foo', [
            'request_object_encryption_enc' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getRequestObjectEncryptionEnc());
    }

    public function testGetAuthorizationSignedResponseAlg(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertNull($metadata->getAuthorizationSignedResponseAlg());

        $metadata = new ClientMetadata('foo', [
            'authorization_signed_response_alg' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getAuthorizationSignedResponseAlg());
    }

    public function testGetTokenEndpointAuthMethod(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertSame('client_secret_basic', $metadata->getTokenEndpointAuthMethod());

        $metadata = new ClientMetadata('foo', [
            'token_endpoint_auth_method' => 'foo',
        ]);

        static::assertSame('foo', $metadata->getTokenEndpointAuthMethod());
    }

    public function testDefaults(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertFalse($metadata->get('require_auth_time'));
        static::assertFalse($metadata->get('tls_client_certificate_bound_access_tokens'));
        static::assertSame(['code'], $metadata->get('response_types'));
        static::assertSame([], $metadata->get('post_logout_redirect_uris'));
        static::assertSame('RS256', $metadata->get('id_token_signed_response_alg'));
        static::assertSame('client_secret_basic', $metadata->getTokenEndpointAuthMethod());
    }

    public function testGet(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertSame('foo', $metadata->get('client_id'));
    }

    public function testHas(): void
    {
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertTrue($metadata->has('client_id'));
        static::assertFalse($metadata->has('foo'));
    }

    public function testJsonSerialize(): void
    {
        $expected = [
            'client_id' => 'foo',
            'redirect_uris' => ['bar'],
        ];
        $metadata = new ClientMetadata('foo', ['redirect_uris' => ['bar']]);

        static::assertSame($expected, json_decode(json_encode($metadata), true));
    }
}
