<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Service;

use function array_filter;
use function array_key_exists;
use function array_merge;
use Facile\OpenIDClient\Client\ClientInterface as OpenIDClient;
use Facile\OpenIDClient\Exception\InvalidArgumentException;
use Facile\OpenIDClient\Exception\OAuth2Exception;
use Facile\OpenIDClient\Exception\RuntimeException;
use function Facile\OpenIDClient\get_endpoint_uri;
use function Facile\OpenIDClient\parse_callback_params;
use function Facile\OpenIDClient\parse_metadata_response;
use Facile\OpenIDClient\Session\AuthSessionInterface;
use Facile\OpenIDClient\Token\IdTokenVerifierBuilder;
use Facile\OpenIDClient\Token\IdTokenVerifierBuilderInterface;
use Facile\OpenIDClient\Token\ResponseVerifierBuilder;
use Facile\OpenIDClient\Token\TokenSetFactory;
use Facile\OpenIDClient\Token\TokenSetFactoryInterface;
use Facile\OpenIDClient\Token\TokenSetInterface;
use Facile\OpenIDClient\Token\TokenVerifierBuilderInterface;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use function http_build_query;
use function is_array;
use function is_string;
use function json_encode;
use JsonSerializable;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * OAuth 2.0
 *
 * @link https://tools.ietf.org/html/rfc6749 RFC 6749
 */
class AuthorizationService
{
    /** @var TokenSetFactoryInterface */
    private $tokenSetFactory;

    /** @var ClientInterface */
    private $client;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var IdTokenVerifierBuilderInterface */
    private $idTokenVerifierBuilder;

    /** @var TokenVerifierBuilderInterface */
    private $responseVerifiierBuilder;

    public function __construct(
        ?TokenSetFactoryInterface $tokenSetFactory = null,
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?IdTokenVerifierBuilderInterface $idTokenVerifierBuilder = null,
        ?TokenVerifierBuilderInterface $responseVerifierBuilder = null
    ) {
        $this->tokenSetFactory = $tokenSetFactory ?? new TokenSetFactory();
        $this->client = $client ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->idTokenVerifierBuilder = $idTokenVerifierBuilder ?? new IdTokenVerifierBuilder();
        $this->responseVerifiierBuilder = $responseVerifierBuilder ?? new ResponseVerifierBuilder();
    }

    /**
     * @param OpenIDClient $client
     * @param array<string, mixed> $params
     *
     * @return string
     */
    public function getAuthorizationUri(OpenIDClient $client, array $params = []): string
    {
        $clientMetadata = $client->getMetadata();
        $issuerMetadata = $client->getIssuer()->getMetadata();
        $endpointUri = $issuerMetadata->getAuthorizationEndpoint();

        $params = array_merge([
            'client_id' => $clientMetadata->getClientId(),
            'scope' => 'openid',
            'response_type' => $clientMetadata->getResponseTypes()[0] ?? 'code',
            'redirect_uri' => $clientMetadata->getRedirectUris()[0] ?? null,
        ], $params);

        $params = array_filter($params, static function ($value): bool {
            return null !== $value;
        });

        foreach ($params as $key => $value) {
            if (null === $value) {
                unset($params[$key]);
            } elseif ('claims' === $key && (is_array($value) || $value instanceof JsonSerializable)) {
                $params['claims'] = json_encode($value);
            } elseif (! is_string($value)) {
                $params[$key] = (string) $value;
            }
        }

        if (! array_key_exists('nonce', $params) && 'code' !== ($params['response_type'] ?? '')) {
            throw new InvalidArgumentException('nonce MUST be provided for implicit and hybrid flows');
        }

        return $endpointUri . '?' . http_build_query($params);
    }

    /**
     * @param ServerRequestInterface $serverRequest
     * @param OpenIDClient $client
     *
     * @return array<string, mixed>
     */
    public function getCallbackParams(ServerRequestInterface $serverRequest, OpenIDClient $client): array
    {
        return $this->processResponseParams($client, parse_callback_params($serverRequest));
    }

    /**
     * @param OpenIDClient $client
     * @param array<string, mixed> $params
     * @param string|null $redirectUri
     * @param AuthSessionInterface|null $authSession
     * @param int|null $maxAge
     *
     * @return TokenSetInterface
     */
    public function callback(
        OpenIDClient $client,
        array $params,
        ?string $redirectUri = null,
        ?AuthSessionInterface $authSession = null,
        ?int $maxAge = null
    ): TokenSetInterface {
        $tokenSet = $this->tokenSetFactory->fromArray($params);

        $idToken = $tokenSet->getIdToken();

        if (null !== $idToken) {
            $claims = $this->idTokenVerifierBuilder->build($client)
                ->withNonce(null !== $authSession ? $authSession->getNonce() : null)
                ->withState(null !== $authSession ? $authSession->getState() : null)
                ->withCode($tokenSet->getCode())
                ->withMaxAge($maxAge)
                ->withAccessToken($tokenSet->getAccessToken())
                ->verify($idToken);
            $tokenSet = $tokenSet->withClaims($claims);
        }

        if (null === $tokenSet->getCode()) {
            return $tokenSet;
        }

        // get token
        return $this->fetchToken($client, $tokenSet, $redirectUri, $authSession, $maxAge);
    }

    public function fetchToken(
        OpenIDClient $client,
        TokenSetInterface $tokenSet,
        ?string $redirectUri = null,
        ?AuthSessionInterface $authSession = null,
        ?int $maxAge = null
    ): TokenSetInterface {
        $code = $tokenSet->getCode();

        if (null === $code) {
            throw new RuntimeException('Unable to fetch token without a code');
        }

        if (null === $redirectUri) {
            $redirectUri = $client->getMetadata()->getRedirectUris()[0] ?? null;
        }

        if (null === $redirectUri) {
            throw new InvalidArgumentException('A redirect_uri should be provided');
        }

        $tokenSet = $this->grant($client, [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ]);

        $idToken = $tokenSet->getIdToken();

        if (null !== $idToken) {
            $claims = $this->idTokenVerifierBuilder->build($client)
                ->withNonce(null !== $authSession ? $authSession->getNonce() : null)
                ->withState(null !== $authSession ? $authSession->getState() : null)
                ->withMaxAge($maxAge)
                ->verify($idToken);
            $tokenSet = $tokenSet->withClaims($claims);
        }

        return $tokenSet;
    }

    /**
     * @param OpenIDClient $client
     * @param string $refreshToken
     * @param array<string, mixed> $params
     *
     * @return TokenSetInterface
     */
    public function refresh(OpenIDClient $client, string $refreshToken, array $params = []): TokenSetInterface
    {
        $tokenSet = $this->grant($client, array_merge($params, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]));

        $idToken = $tokenSet->getIdToken();

        if (null === $idToken) {
            return $tokenSet;
        }

        $idToken = $tokenSet->getIdToken();

        if (null !== $idToken) {
            $claims = $this->idTokenVerifierBuilder->build($client)
                ->withAccessToken($tokenSet->getAccessToken())
                ->verify($idToken);
            $tokenSet = $tokenSet->withClaims($claims);
        }

        return $tokenSet;
    }

    /**
     * @param OpenIDClient $client
     * @param array<string, mixed> $params
     *
     * @return TokenSetInterface
     */
    public function grant(OpenIDClient $client, array $params = []): TokenSetInterface
    {
        $authMethod = $client->getAuthMethodFactory()
            ->create($client->getMetadata()->getTokenEndpointAuthMethod());

        $endpointUri = get_endpoint_uri($client, 'token_endpoint');

        $tokenRequest = $this->requestFactory->createRequest('POST', $endpointUri)
            ->withHeader('content-type', 'application/x-www-form-urlencoded');

        $tokenRequest = $authMethod->createRequest($tokenRequest, $client, $params);

        $httpClient = $client->getHttpClient() ?? $this->client;

        try {
            $response = $httpClient->sendRequest($tokenRequest);
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException('Unable to get token response', 0, $e);
        }

        $params = $this->processResponseParams($client, parse_metadata_response($response));

        return $this->tokenSetFactory->fromArray($params);
    }

    /**
     * @param OpenIDClient $client
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function processResponseParams(OpenIDClient $client, array $params): array
    {
        if (array_key_exists('error', $params)) {
            throw OAuth2Exception::fromParameters($params);
        }

        if (array_key_exists('response', $params)) {
            $params = $this->responseVerifiierBuilder->build($client)
                ->verify($params['response']);
        }

        if (array_key_exists('error', $params)) {
            throw OAuth2Exception::fromParameters($params);
        }

        return $params;
    }
}
