<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata\Provider;

use Facile\OpenIDClient\Exception\InvalidArgumentException;
use Facile\OpenIDClient\Exception\RuntimeException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

use function array_key_exists;
use function array_pop;
use function explode;
use function Facile\OpenIDClient\parse_metadata_response;
use function http_build_query;
use function is_array;
use function is_string;
use function parse_url;
use function preg_match;
use function preg_replace;

final class WebFingerProvider implements RemoteProviderInterface, WebFingerProviderInterface
{
    private const AAD_MULTITENANT_DISCOVERY = 'https://login.microsoftonline.com/common/v2.0$' . self::OIDC_DISCOVERY;

    private const OIDC_DISCOVERY = '/.well-known/openid-configuration';

    private const REL = 'http://openid.net/specs/connect/1.0/issuer';

    private const WEBFINGER = '/.well-known/webfinger';

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var DiscoveryProviderInterface
     */
    private $discoveryProvider;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var UriFactoryInterface
     */
    private $uriFactory;

    public function __construct(
        ClientInterface $client,
        RequestFactoryInterface $requestFactory,
        UriFactoryInterface $uriFactory,
        DiscoveryProviderInterface $discoveryProvider
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->discoveryProvider = $discoveryProvider;
    }

    public function fetch(string $resource): array
    {
        $resource = $this->normalizeWebfinger($resource);
        $parsedUrl = parse_url(
            false !== mb_strpos($resource, '@')
                ? 'https://' . explode('@', $resource)[1]
                : $resource
        );

        if (!is_array($parsedUrl) || !array_key_exists('host', $parsedUrl)) {
            throw new RuntimeException('Unable to parse resource');
        }

        $host = $parsedUrl['host'];

        /** @var int|string|null $port */
        $port = $parsedUrl['port'] ?? null;

        if (0 < ((int) $port)) {
            $host .= ':' . ((int) $port);
        }

        $webFingerUrl = $this->uriFactory->createUri('https://' . $host . self::WEBFINGER)
            ->withQuery(http_build_query(['resource' => $resource, 'rel' => self::REL]));

        $request = $this->requestFactory->createRequest('GET', $webFingerUrl)
            ->withHeader('accept', 'application/json');

        try {
            $data = parse_metadata_response($this->client->sendRequest($request));
        } catch (ClientExceptionInterface $e) {
            throw new RuntimeException('Unable to fetch provider metadata', 0, $e);
        }

        /** @var array<array-key, null|array{rel?: string, href?: string}> $links */
        $links = $data['links'] ?? [];
        $href = null;

        foreach ($links as $link) {
            if (!is_array($link)) {
                continue;
            }

            if (self::REL !== ($link['rel'] ?? null)) {
                continue;
            }

            if (!array_key_exists('href', $link)) {
                continue;
            }

            $href = $link['href'];
        }

        if (!is_string($href) || 0 !== mb_strpos($href, 'https://')) {
            throw new InvalidArgumentException('Invalid issuer location');
        }

        $metadata = $this->discoveryProvider->discovery($href);

        if (($metadata['issuer'] ?? null) !== $href) {
            throw new RuntimeException('Discovered issuer mismatch');
        }

        return $metadata;
    }

    public function isAllowedUri(string $uri): bool
    {
        return true;
    }

    private function normalizeWebfinger(string $input): string
    {
        $hasScheme = static function (string $resource): bool {
            if (false !== mb_strpos($resource, '://')) {
                return true;
            }

            $authority = explode('#', (string) preg_replace('/(\/|\?)/', '#', $resource))[0];

            if (false === ($index = mb_strpos($authority, ':'))) {
                return false;
            }

            $hostOrPort = mb_substr($resource, $index + 1);

            return !(bool) preg_match('/^\d+$/', $hostOrPort);
        };

        $acctSchemeAssumed = static function (string $input): bool {
            if (false === mb_strpos($input, '@')) {
                return false;
            }

            $parts = explode('@', $input);
            /** @var string $host */
            $host = array_pop($parts);

            return !(bool) preg_match('/[:\/?]+/', $host);
        };

        if ($hasScheme($input)) {
            $output = $input;
        } elseif ($acctSchemeAssumed($input)) {
            $output = 'acct:' . $input;
        } else {
            $output = 'https://' . $input;
        }

        return explode('#', $output)[0];
    }
}
