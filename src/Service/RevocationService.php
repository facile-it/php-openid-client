<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Service;

use function Facile\OpenIDClient\check_server_response;
use Facile\OpenIDClient\Client\ClientInterface as OpenIDClient;
use Facile\OpenIDClient\Exception\RuntimeException;
use function Facile\OpenIDClient\get_endpoint_uri;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * RFC 7009 Token Revocation
 *
 * @link https://tools.ietf.org/html/rfc7009 RFC 7009
 */
final class RevocationService
{
    /** @var ClientInterface */
    private $client;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @param OpenIDClient $client
     * @param string $token
     * @param array<string, mixed> $params
     */
    public function revoke(OpenIDClient $client, string $token, array $params = []): void
    {
        $endpointUri = get_endpoint_uri($client, 'revocation_endpoint');

        $authMethod = $client->getAuthMethodFactory()
            ->create($client->getMetadata()->getRevocationEndpointAuthMethod());

        $tokenRequest = $this->requestFactory->createRequest('POST', $endpointUri)
            ->withHeader('content-type', 'application/x-www-form-urlencoded');

        $tokenRequest = $authMethod->createRequest($tokenRequest, $client, $params);
        $tokenRequest->getBody()->write(http_build_query(array_merge($params, [
            'token' => $token,
        ])));

        $httpClient = $client->getHttpClient() ?? $this->client;

        try {
            $response = $httpClient->sendRequest($tokenRequest);
            check_server_response($response, 200);
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException('Unable to get revocation response', 0, $e);
        }
    }
}
