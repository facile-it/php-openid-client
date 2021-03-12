<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Service\Builder;

use Facile\OpenIDClient\Service\AuthorizationService;
use Facile\OpenIDClient\Token\IdTokenVerifierBuilder;
use Facile\OpenIDClient\Token\IdTokenVerifierBuilderInterface;
use Facile\OpenIDClient\Token\ResponseVerifierBuilder;
use Facile\OpenIDClient\Token\TokenSetFactory;
use Facile\OpenIDClient\Token\TokenSetFactoryInterface;
use Facile\OpenIDClient\Token\TokenVerifierBuilderInterface;

final class AuthorizationServiceBuilder extends AbstractServiceBuilder
{
    /**
     * @var IdTokenVerifierBuilderInterface|null
     */
    private $idTokenVerifierBuilder;

    /**
     * @var TokenVerifierBuilderInterface|null
     */
    private $responseVerifierBuilder;

    /**
     * @var TokenSetFactoryInterface|null
     */
    private $tokenSetFactory;

    public function build(): AuthorizationService
    {
        return new AuthorizationService(
            $this->getTokenSetFactory(),
            $this->getHttpClient(),
            $this->getRequestFactory(),
            $this->getIdTokenVerifierBuilder(),
            $this->getResponseVerifierBuilder()
        );
    }

    public function setIdTokenVerifierBuilder(IdTokenVerifierBuilderInterface $idTokenVerifierBuilder): self
    {
        $this->idTokenVerifierBuilder = $idTokenVerifierBuilder;

        return $this;
    }

    public function setResponseVerifierBuilder(TokenVerifierBuilderInterface $responseVerifierBuilder): self
    {
        $this->responseVerifierBuilder = $responseVerifierBuilder;

        return $this;
    }

    public function setTokenSetFactory(TokenSetFactoryInterface $tokenSetFactory): self
    {
        $this->tokenSetFactory = $tokenSetFactory;

        return $this;
    }

    protected function getIdTokenVerifierBuilder(): IdTokenVerifierBuilderInterface
    {
        return $this->idTokenVerifierBuilder = $this->idTokenVerifierBuilder ?? new IdTokenVerifierBuilder();
    }

    protected function getResponseVerifierBuilder(): TokenVerifierBuilderInterface
    {
        return $this->responseVerifierBuilder = $this->responseVerifierBuilder ?? new ResponseVerifierBuilder();
    }

    protected function getTokenSetFactory(): TokenSetFactoryInterface
    {
        return $this->tokenSetFactory = $this->tokenSetFactory ?? new TokenSetFactory();
    }
}
