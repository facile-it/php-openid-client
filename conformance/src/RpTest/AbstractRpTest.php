<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest;

use Facile\JoseVerifier\JWK\MemoryJwksProvider;
use Facile\OpenIDClient\Client\ClientBuilder;
use Facile\OpenIDClient\Client\ClientInterface;
use Facile\OpenIDClient\Client\Metadata\ClientMetadata;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Facile\OpenIDClient\Exception\OAuth2Exception;
use Facile\OpenIDClient\Exception\RemoteException;
use Facile\OpenIDClient\Issuer\IssuerBuilder;
use Facile\OpenIDClient\Service\RegistrationService;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Jose\Component\Core\JWKSet;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_merge;
use function json_decode;
use function json_encode;
use function sprintf;

use const PHP_EOL;

abstract class AbstractRpTest implements RpTestInterface
{
    protected const REDIRECT_URI = 'https://rp.test/callback';

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function registerClient(TestInfo $testInfo, array $metadata = [], ?JWKSet $jwks = null): ClientInterface
    {
        $issuer = (new IssuerBuilder())
            ->build($testInfo->getRpUri() . '/' . $this->getTestId() . '/.well-known/openid-configuration');

        $registrationService = new RegistrationService();

        $metadata = array_merge([
            'client_name' => 'php-openid-client/v1.x (https://github.com/facile-it/php-openid-client)',
            'redirect_uris' => [static::REDIRECT_URI],
            'contacts' => [
                'tvargiu@gmail.com',
            ],
            'grant_types' => [
                'authorization_code',
                'implicit',
            ],
            'response_types' => [
                $testInfo->getResponseType(),
            ],
        ], $metadata);

        try {
            $clientMetadata = ClientMetadata::fromArray($registrationService->register($issuer, $metadata));
        } catch (OAuth2Exception $e) {
            echo sprintf('%s (%s)', $e->getMessage(), $e->getDescription()) . PHP_EOL;
            throw $e;
        } catch (RemoteException $e) {
            echo $e->getResponse()->getBody() . PHP_EOL;
            throw $e;
        }

        return (new ClientBuilder())
            ->setIssuer($issuer)
            ->setClientMetadata($clientMetadata)
            ->setJwksProvider(new MemoryJwksProvider(json_decode(json_encode($jwks ?? ['keys' => []]), true)))
            ->build();
    }

    protected function httpGet(string $uri, array $headers = []): ResponseInterface
    {
        /** @var HttpClient $client */
        $httpClient = $this->getContainer()->has(HttpClient::class)
            ? $this->getContainer()->get(HttpClient::class)
            : Psr18ClientDiscovery::find();
        $requestFactory = $this->getContainer()->has(RequestFactoryInterface::class)
            ? $this->getContainer()->get(RequestFactoryInterface::class)
            : Psr17FactoryDiscovery::findRequestFactory();

        $request = $requestFactory->createRequest('GET', $uri);

        foreach ($headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        return $httpClient->sendRequest($request);
    }

    protected function simulateAuthRedirect(string $uri, string $accept = 'application/json'): ServerRequestInterface
    {
        $response = $this->httpGet($uri, ['accept' => $accept]);

        $serverRequestFactory = new ServerRequestFactory();

        /** @var string $location */
        $location = $response->getHeader('location')[0] ?? null;

        return $serverRequestFactory->createServerRequest('GET', $location);
    }
}
