<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Issuer\Metadata\Provider;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\SimpleCache\CacheInterface;

class MetadataProviderBuilder
{
    /**
     * @var CacheInterface|null
     */
    private $cache;

    /**
     * @var int|null
     */
    private $cacheTtl;

    /**
     * @var ClientInterface|null
     */
    private $client;

    /**
     * @var DiscoveryProviderInterface|null
     */
    private $discoveryProvider;

    /**
     * @var RequestFactoryInterface|null
     */
    private $requestFactory;

    /**
     * @var UriFactoryInterface|null
     */
    private $uriFactory;

    /**
     * @var WebFingerProviderInterface|null
     */
    private $webFingerProvider;

    public function build(): RemoteProviderInterface
    {
        $provider = new RemoteProvider([
            $this->buildDiscoveryProvider(),
            $this->buildWebFingerProvider(),
        ]);

        if (null !== $this->cache) {
            $provider = new CachedProviderDecorator($provider, $this->cache, $this->cacheTtl);
        }

        return $provider;
    }

    public function buildRequestFactory(): RequestFactoryInterface
    {
        return $this->requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    }

    public function buildUriFactory(): UriFactoryInterface
    {
        return $this->uriFactory ?? Psr17FactoryDiscovery::findUriFactory();
    }

    public function buildWebFingerProvider(): WebFingerProviderInterface
    {
        return $this->webFingerProvider ?? new WebFingerProvider(
            $this->buildClient(),
            $this->buildRequestFactory(),
            $this->buildUriFactory(),
            $this->buildDiscoveryProvider()
        );
    }

    public function setCache(?CacheInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }

    public function setCacheTtl(?int $cacheTtl): self
    {
        $this->cacheTtl = $cacheTtl;

        return $this;
    }

    public function setClient(?ClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function setDiscoveryProvider(DiscoveryProviderInterface $discoveryProvider): self
    {
        $this->discoveryProvider = $discoveryProvider;

        return $this;
    }

    public function setRequestFactory(?RequestFactoryInterface $requestFactory): self
    {
        $this->requestFactory = $requestFactory;

        return $this;
    }

    public function setUriFactory(?UriFactoryInterface $uriFactory): self
    {
        $this->uriFactory = $uriFactory;

        return $this;
    }

    public function setWebFingerProvider(WebFingerProviderInterface $webFingerProvider): self
    {
        $this->webFingerProvider = $webFingerProvider;

        return $this;
    }

    private function buildClient(): ClientInterface
    {
        return $this->client ?? Psr18ClientDiscovery::find();
    }

    private function buildDiscoveryProvider(): DiscoveryProviderInterface
    {
        return $this->discoveryProvider ?? new DiscoveryProvider(
            $this->buildClient(),
            $this->buildRequestFactory(),
            $this->buildUriFactory()
        );
    }
}
