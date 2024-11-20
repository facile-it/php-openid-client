<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\Token;

use Facile\JoseVerifier\TokenVerifierInterface;
use Facile\OpenIDClient\Client\ClientInterface;

final class UserInfoVerifierBuilder implements TokenVerifierBuilderInterface
{
    /** @var bool */
    private $aadIssValidation = false;

    /** @var int */
    private $clockTolerance = 0;

    public function setAadIssValidation(bool $aadIssValidation): self
    {
        $this->aadIssValidation = $aadIssValidation;

        return $this;
    }

    public function setClockTolerance(int $clockTolerance): self
    {
        $this->clockTolerance = $clockTolerance;

        return $this;
    }

    public function build(ClientInterface $client): TokenVerifierInterface
    {
        return \Facile\JoseVerifier\Builder\UserInfoVerifierBuilder::create(
            $client->getIssuer()->getMetadata()->toArray(),
            $client->getMetadata()->toArray(),
        )->build();
    }
}
