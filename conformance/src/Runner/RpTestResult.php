<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\Runner;

use Facile\OpenIDClient\ConformanceTest\RpTest\RpTestInterface;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Throwable;

class RpTestResult
{
    public function __construct(
        private readonly RpTestInterface $test,
        private readonly TestInfo $testInfo,
        private readonly string $implementation,
        /** @var Throwable */
        private ?Throwable $exception = null
    ) {}

    public function getTest(): RpTestInterface
    {
        return $this->test;
    }

    public function getTestInfo(): TestInfo
    {
        return $this->testInfo;
    }

    public function getImplementation(): string
    {
        return $this->implementation;
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    public function setException(Throwable $exception): void
    {
        $this->exception = $exception;
    }
}
