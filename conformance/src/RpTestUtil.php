<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest;

use function fclose;
use function file_exists;
use function fopen;
use function fwrite;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use function is_dir;
use function ltrim;
use function mkdir;
use function preg_match;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use function sprintf;

class RpTestUtil
{
    /** @var ClientInterface */
    private $client;

    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var string */
    private $logDir;

    public function __construct(
        ?ClientInterface $client = null,
        ?RequestFactoryInterface $requestFactory = null,
        string $logDir = __DIR__ . '/../log'
    ) {
        $this->client = $client ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
        $this->logDir = $logDir;
    }

    private function mkdir(string $dirname): void
    {
        if (! file_exists($dirname) && ! mkdir($concurrentDirectory = $dirname, 0777, true) && ! is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    private function get(string $uri): ResponseInterface
    {
        $request = $this->requestFactory->createRequest('GET', $uri);

        return $this->client->sendRequest($request);
    }

    public function clearLogs(string $root, string $rpId): void
    {
        $response = $this->get($root . '/log/' . $rpId);

        if (! preg_match('/Clear all test logs/', (string) $response->getBody())) {
            return;
        }

        $this->get($root . '/clear/' . $rpId);
    }

    public function downloadLogs(TestInfo $testInfo, string $profile, string $responseType): void
    {
        $response = $this->get($testInfo->getRpLogsUri());

        if (! preg_match('/Download tar file/', (string) $response->getBody())) {
            return;
        }

        $logDir = $this->logDir . '/' . ltrim($profile, '@');
        $this->mkdir($logDir);

        $response = $this->get($testInfo->getRoot() . '/mktar/' . $testInfo->getRpUri());
        $handle = fopen($logDir . '/log-' . $responseType . '.tar', 'wb+');

        while (! $response->getBody()->eof()) {
            fwrite($handle, $response->getBody()->getContents());
        }
        fclose($handle);
    }
}
