<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\AuthMethod;

use Facile\OpenIDClient\Client\ClientInterface as OpenIDClient;
use Psr\Http\Message\RequestInterface;

interface AuthMethodInterface
{
    public const TLS_METHODS = [
        'self_signed_tls_client_auth',
        'tls_client_auth',
    ];

    /**
     * @param array<string, mixed> $claims
     */
    public function createRequest(RequestInterface $request, OpenIDClient $client, array $claims): RequestInterface;

    public function getSupportedMethod(): string;
}
