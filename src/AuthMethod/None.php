<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\AuthMethod;

use Facile\OpenIDClient\Client\ClientInterface as OpenIDClient;
use function http_build_query;
use Psr\Http\Message\RequestInterface;

final class None implements AuthMethodInterface
{
    public function getSupportedMethod(): string
    {
        return 'none';
    }

    public function createRequest(
        RequestInterface $request,
        OpenIDClient $client,
        array $claims
    ): RequestInterface {
        $request->getBody()->write(http_build_query($claims));

        return $request;
    }
}
