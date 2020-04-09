<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\Helper;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class RPLogsHelper
{
    /** @var ClientInterface */
    private $client;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    public function __construct(?ClientInterface $client = null, ?RequestFactoryInterface $requestFactory = null)
    {
        $this->client = $client ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    }

    public function clearLogs(string $root, string $rpId): void
    {
        $request = $this->requestFactory->createRequest('GET', $root . '/log/' . $rpId);
        $response = $this->client->sendRequest($request);

        if (! \preg_match('/Clear all test logs/', (string) $response->getBody())) {
            return;
        }

        $request = $this->requestFactory->createRequest('GET', $root . '/clear/' . $rpId);

        $this->client->sendRequest($request);
    }

    public function getLog(string $root, string $rpId, string $testId): ?ResponseInterface
    {
        $request = $this->requestFactory->createRequest('GET', $root . '/log/' . $rpId . '/' . $testId . '.txt');

        return $this->client->sendRequest($request);
    }

    public function downloadLogs(string $root, string $rpId): ?ResponseInterface
    {
        $request = $this->requestFactory->createRequest('GET', $root . '/log/' . $rpId);
        $response = $this->client->sendRequest($request);

        if (! \preg_match('/Download tar file/', (string) $response->getBody())) {
            return null;
        }

        $request = $this->requestFactory->createRequest('GET', $root . '/mktar/' . $rpId);

        return $this->client->sendRequest($request);
    }
}
