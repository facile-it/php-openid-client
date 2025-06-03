<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest;

use Facile\OpenIDClient\Client\ClientBuilder;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use Facile\OpenIDClient\Service\RegistrationService;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionFunction;
use RuntimeException;
use Throwable;

use function array_merge;
use function dirname;
use function file;
use function file_exists;
use function file_put_contents;
use function implode;
use function is_dir;
use function ltrim;
use function mkdir;
use function parse_str;
use function sprintf;
use function var_export;

use const PHP_EOL;

abstract class AbstractRpTestCase extends TestCase
{
    /** @var ContainerInterface */
    protected static $container;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$container = require __DIR__ . '/../config/container.php';
    }

    public function getRootTestUri(): string
    {
        return 'https://rp.certification.openid.net:8080/';
    }

    public function getRpID(): string
    {
        return 'tmv_php-openid-client';
    }

    public function getRpUri(): string
    {
        return $this->getRootTestUri() . $this->getRpID() . '/';
    }

    public function getTestUri(string $testId): string
    {
        return $this->getRpUri() . $testId;
    }

    public function getLogTestUri(string $testId): string
    {
        return $this->getRootTestUri() . 'log/' . $this->getRpID() . '/' . $testId . '.txt';
    }

    public function getRedirectUri(): string
    {
        return 'https://' . $this->getRpID() . '.dev/callback';
    }

    protected function simulateAuthRedirect(string $uri): ServerRequestInterface
    {
        /** @var HttpClient $client */
        $httpClient = $this->getContainer()->has(HttpClient::class)
            ? $this->getContainer()->get(HttpClient::class)
            : Psr18ClientDiscovery::find();
        $requestFactory = $this->getContainer()->has(RequestFactoryInterface::class)
            ? $this->getContainer()->get(RequestFactoryInterface::class)
            : Psr17FactoryDiscovery::findRequestFactory();

        $request = $requestFactory->createRequest('GET', $uri);
        $response = $httpClient->sendRequest($request);

        $serverRequestFactory = new ServerRequestFactory();

        /** @var string $location */
        $location = $response->getHeader('location')[0] ?? null;
        $this->assertIsString($location);

        return $serverRequestFactory->createServerRequest('GET', $location);
    }

    protected function parseQueryParams(string $uri): array
    {
        $uri = new Uri($uri);
        parse_str($uri->getQuery(), $query);

        return $query;
    }

    protected function executeRpTest(string $profile, string $testName, callable $callback): void
    {
        echo $this->getClosureDump($callback);

        $testUtil = $this->getContainer()->get(RpTestUtil::class);

        try {
            $callback($profile, $testName);
        } catch (Throwable $e) {
            throw $e;
        } finally {
            $this->getAndSaveTestLog($profile, $testName);
        }
    }

    public function registerClient(string $testName, array $metadata = []): ClientInterface
    {
        $registrationService = new RegistrationService();

        $issuerBuilder = new IssuerBuilder();
        $issuer = $issuerBuilder->build($this->getTestUri($testName) . '/.well-known/openid-configuration');

        $clientMetadata = ClientMetadata::fromArray($registrationService->register($issuer, array_merge([
            'redirect_uris' => [$this->getRedirectUri()],
            'contacts' => [
                'tvargiu@gmail.com',
            ],
        ], $metadata)));

        return (new ClientBuilder())
            ->setClientMetadata($clientMetadata)
            ->build();
    }

    protected function getClosureDump(callable $closure)
    {
        $str = 'function (';
        $r = new ReflectionFunction($closure);
        $params = [];
        foreach ($r->getParameters() as $p) {
            $s = '';
            if ($p->isArray()) {
                $s .= 'array ';
            } elseif ($p->getClass()) {
                $s .= $p->getClass()->name . ' ';
            }
            if ($p->isPassedByReference()) {
                $s .= '&';
            }
            $s .= '$' . $p->name;
            if ($p->isOptional()) {
                $s .= ' = ' . var_export($p->getDefaultValue(), true);
            }
            $params[] = $s;
        }
        $str .= implode(', ', $params);
        $str .= '){' . PHP_EOL;
        $lines = file($r->getFileName());
        for ($l = $r->getStartLine(); $l < $r->getEndLine(); ++$l) {
            $str .= $lines[$l];
        }

        return $str;
    }

    public function getAndSaveTestLog(string $profile, string $testName): string
    {
        /** @var HttpClient $httpClient */
        $httpClient = $this->getContainer()->get('httplug.clients.default');
        $request = (new RequestFactory())->createRequest('GET', $this->getLogTestUri($testName));

        $response = $httpClient->sendRequest($request);

        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException('Invalid log response status code');
        }

        $log = (string) $response->getBody();

        $logFilePath = __DIR__ . '/../log/' . ltrim($profile, '@') . '/' . $testName . '.txt';
        $dirname = dirname($logFilePath);

        if (! file_exists($dirname) && ! mkdir($concurrentDirectory = $dirname, 0o777, true) && ! is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        file_put_contents($logFilePath, $log);

        return $log;
    }

    public function getRpProfile(): ?string
    {
        $value = $this->getAnnotations()['method']['rp-profile'][0] ?? null;

        if (! $value) {
            throw new RuntimeException('No rp-profile annotation set');
        }

        return $value;
    }

    public function getRpTestId(): ?string
    {
        $value = $this->getAnnotations()['method']['rp-test-id'][0] ?? null;

        if (! $value) {
            throw new RuntimeException('No rp-test-id annotation set');
        }

        return $value;
    }

    public function getContainer(): ContainerInterface
    {
        return static::$container;
    }
}
