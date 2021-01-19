<?php

declare(strict_types=1);

namespace Facile\OpenIDClientTest\Service;

use Facile\OpenIDClient\AuthMethod\AuthMethodFactoryInterface;
use Facile\OpenIDClient\AuthMethod\AuthMethodInterface;
use Facile\OpenIDClient\Client\ClientInterface as OpenIDClient;
use Facile\OpenIDClient\Client\Metadata\ClientMetadataInterface;
use Facile\OpenIDClient\Issuer\IssuerInterface;
use Facile\OpenIDClient\Issuer\Metadata\IssuerMetadataInterface;
use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Token\IdTokenVerifierBuilderInterface;
use Facile\OpenIDClient\Token\TokenSetFactoryInterface;
use Facile\OpenIDClient\Token\TokenSetInterface;
use Facile\OpenIDClient\Token\TokenVerifierBuilderInterface;
use Facile\OpenIDClientTest\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class AuthorizationServiceTest extends TestCase
{
    public function testGetAuthorizationUri(): void
    {
        $tokenSetFactory = $this->prophesize(TokenSetFactoryInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $idTokenVerifierBuilder = $this->prophesize(IdTokenVerifierBuilderInterface::class);
        $tokenVerifierBuilder = $this->prophesize(TokenVerifierBuilderInterface::class);

        $service = new AuthorizationService(
            $tokenSetFactory->reveal(),
            $client->reveal(),
            $requestFactory->reveal(),
            $idTokenVerifierBuilder->reveal(),
            $tokenVerifierBuilder->reveal()
        );

        $openIdClient = $this->prophesize(OpenIDClient::class);
        $clientMetadata = $this->prophesize(ClientMetadataInterface::class);
        $issuer = $this->prophesize(IssuerInterface::class);
        $issuerMetadata = $this->prophesize(IssuerMetadataInterface::class);

        $openIdClient->getIssuer()->willReturn($issuer->reveal());
        $openIdClient->getMetadata()->willReturn($clientMetadata->reveal());
        $openIdClient->getHttpClient()->willReturn(null);
        $clientMetadata->getClientId()->willReturn('clientId');
        $clientMetadata->getResponseTypes()->willReturn(['code']);
        $clientMetadata->getRedirectUris()->willReturn(['redirect_uri_1']);
        $issuer->getMetadata()->willReturn($issuerMetadata);
        $issuerMetadata->getAuthorizationEndpoint()->willReturn('https://foo-endpoint');

        static::assertSame('https://foo-endpoint?client_id=clientId&scope=openid&response_type=code&redirect_uri=redirect_uri_1', $service->getAuthorizationUri($openIdClient->reveal()));
    }

    public function testFetchTokenFromCode(): void
    {
        $tokenSetFactory = $this->prophesize(TokenSetFactoryInterface::class);
        $client = $this->prophesize(ClientInterface::class);
        $requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $idTokenVerifierBuilder = $this->prophesize(IdTokenVerifierBuilderInterface::class);
        $tokenVerifierBuilder = $this->prophesize(TokenVerifierBuilderInterface::class);

        $service = new AuthorizationService(
            $tokenSetFactory->reveal(),
            $client->reveal(),
            $requestFactory->reveal(),
            $idTokenVerifierBuilder->reveal(),
            $tokenVerifierBuilder->reveal()
        );

        $openIdClient = $this->prophesize(OpenIDClient::class);
        $openIdClient->getHttpClient()->willReturn(null);
        $metadata = $this->prophesize(ClientMetadataInterface::class);
        $authMethodFactory = $this->prophesize(AuthMethodFactoryInterface::class);
        $authMethod = $this->prophesize(AuthMethodInterface::class);
        $request = $this->prophesize(RequestInterface::class);
        $tokenRequest1 = $this->prophesize(RequestInterface::class);
        $tokenRequest2 = $this->prophesize(RequestInterface::class);
        $response = $this->prophesize(ResponseInterface::class);
        $stream = $this->prophesize(StreamInterface::class);
        $issuer = $this->prophesize(IssuerInterface::class);
        $issuerMetadata = $this->prophesize(IssuerMetadataInterface::class);

        $claims = [
            'grant_type' => 'authorization_code',
            'code' => 'foo-code',
            'redirect_uri' => 'redirect-uri',
        ];

        $requestFactory->createRequest('POST', 'token-endpoint')
            ->willReturn($request->reveal());
        $request->withHeader('content-type', 'application/x-www-form-urlencoded')
            ->willReturn($tokenRequest1->reveal());
        $openIdClient->getIssuer()->willReturn($issuer->reveal());
        $issuer->getMetadata()->willReturn($issuerMetadata->reveal());
        $issuerMetadata->getTokenEndpoint()->willReturn('token-endpoint');
        $issuerMetadata->get('token_endpoint')->willReturn('token-endpoint');
        $openIdClient->getMetadata()->willReturn($metadata->reveal());
        $openIdClient->getAuthMethodFactory()->willReturn($authMethodFactory->reveal());
        $metadata->getTokenEndpointAuthMethod()->willReturn('auth-method');
        $metadata->get('token_endpoint_auth_method')->willReturn('auth-method');
        $authMethodFactory->create('auth-method')->willReturn($authMethod->reveal());
        $authMethod->createRequest(
            $tokenRequest1->reveal(),
            $openIdClient->reveal(),
            $claims
        )
            ->willReturn($tokenRequest2->reveal());

        $client->sendRequest($tokenRequest2->reveal())
            ->willReturn($response->reveal());
        $response->getStatusCode()->willReturn(200);
        $response->getBody()->willReturn($stream->reveal());
        $stream->__toString()->willReturn('{"foo":"bar"}');

        $tokenSet = $this->prophesize(TokenSetInterface::class);
        $tokenSetFactory->fromArray(['foo' => 'bar'])->willReturn($tokenSet->reveal());

        static::assertSame($tokenSet->reveal(), $service->grant($openIdClient->reveal(), $claims));
    }
}
