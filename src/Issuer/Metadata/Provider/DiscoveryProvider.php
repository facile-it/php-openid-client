<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata\Provider;

use function array_key_exists;
use Facile\OpenIDClient\Exception\RuntimeException;
use function Facile\OpenIDClient\parse_metadata_response;
use function preg_match;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use function rtrim;
use function strpos;

final class DiscoveryProvider implements DiscoveryProviderInterface
{
    private const OIDC_DISCOVERY = '/.well-known/openid-configuration';

    private const OAUTH2_DISCOVERY = '/.well-known/oauth-authorization-server';

    /** @var ClientInterface */
    private $client;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var UriFactoryInterface */
    private $uriFactory;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
    }

    public function isAllowedUri(string $uri): bool
    {
        return (int) preg_match('/https?:\/\//', $uri) > 0;
    }

    public function discovery(string $url): array
    {
        $uri = $this->uriFactory->createUri($url);
        $uriPath = $uri->getPath() ?? '/';

        if (false !== strpos($uriPath, '/.well-known/')) {
            return $this->fetchOpenIdConfiguration((string) $uri);
        }

        $uris = [
            $uri->withPath(rtrim($uriPath, '/') . self::OIDC_DISCOVERY),
            $uri->withPath('/' === $uriPath
                ? self::OAUTH2_DISCOVERY
                : self::OAUTH2_DISCOVERY . $uriPath),
        ];

        foreach ($uris as $wellKnownUri) {
            try {
                return $this->fetchOpenIdConfiguration((string) $wellKnownUri);
            } catch (RuntimeException $e) {
            }
        }

        throw new RuntimeException('Unable to fetch provider metadata');
    }

    /**
     * @param string $uri
     *
     * @return array<mixed, string>
     * @phpstan-return OpenIDDiscoveryConfiguration
     */
    private function fetchOpenIdConfiguration(string $uri): array
    {
        $request = $this->requestFactory->createRequest('GET', $uri)
            ->withHeader('accept', 'application/json');

        try {
            $data = parse_metadata_response($this->client->sendRequest($request));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException('Unable to fetch provider metadata', 0, $e);
        }

        if (! array_key_exists('issuer', $data)) {
            throw new RuntimeException('Invalid metadata content, no "issuer" key found');
        }

        return $data;
    }

    public function fetch(string $uri): array
    {
        return $this->discovery($uri);
    }
}
