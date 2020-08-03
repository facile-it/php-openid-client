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

class AuthorizationServiceBuilder extends AbstractServiceBuilder
{
    /** @var null|TokenSetFactoryInterface */
    private $tokenSetFactory;

    /** @var null|IdTokenVerifierBuilderInterface */
    private $idTokenVerifierBuilder;

    /** @var null|TokenVerifierBuilderInterface */
    private $responseVerifierBuilder;

    public function setTokenSetFactory(TokenSetFactoryInterface $tokenSetFactory): void
    {
        $this->tokenSetFactory = $tokenSetFactory;
    }

    public function setIdTokenVerifierBuilder(IdTokenVerifierBuilderInterface $idTokenVerifierBuilder): void
    {
        $this->idTokenVerifierBuilder = $idTokenVerifierBuilder;
    }

    public function setResponseVerifierBuilder(TokenVerifierBuilderInterface $responseVerifierBuilder): void
    {
        $this->responseVerifierBuilder = $responseVerifierBuilder;
    }

    protected function getTokenSetFactory(): TokenSetFactoryInterface
    {
        return $this->tokenSetFactory = $this->tokenSetFactory ?? new TokenSetFactory();
    }

    protected function getIdTokenVerifierBuilder(): IdTokenVerifierBuilderInterface
    {
        return $this->idTokenVerifierBuilder = $this->idTokenVerifierBuilder ?? new IdTokenVerifierBuilder();
    }

    protected function getResponseVerifierBuilder(): TokenVerifierBuilderInterface
    {
        return $this->responseVerifierBuilder = $this->responseVerifierBuilder ?? new ResponseVerifierBuilder();
    }

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
}
