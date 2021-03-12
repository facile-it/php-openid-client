<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\AuthMethod;

use Facile\OpenIDClient\Client\ClientInterface as OpenIDClient;
use Psr\Http\Message\RequestInterface;

use function array_merge;
use function http_build_query;

final class None implements AuthMethodInterface
{
    public function createRequest(
        RequestInterface $request,
        OpenIDClient $client,
        array $claims
    ): RequestInterface {
        $params = array_merge(['client_id' => $client->getMetadata()->getClientId()], $claims);
        $request->getBody()->write(http_build_query($params));

        return $request;
    }

    public function getSupportedMethod(): string
    {
        return 'none';
    }
}
