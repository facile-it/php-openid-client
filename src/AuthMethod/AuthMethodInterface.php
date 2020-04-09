<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\AuthMethod;

use Psr\Http\Message\RequestInterface;
use Facile\OpenIDClient\Client\ClientInterface as OpenIDClient;

interface AuthMethodInterface
{
    public const TLS_METHODS = [
        'self_signed_tls_client_auth',
        'tls_client_auth',
    ];

    public function getSupportedMethod(): string;

    /**
     * @param RequestInterface $request
     * @param OpenIDClient $client
     * @param array<string, mixed> $claims
     *
     * @return RequestInterface
     */
    public function createRequest(RequestInterface $request, OpenIDClient $client, array $claims): RequestInterface;
}
