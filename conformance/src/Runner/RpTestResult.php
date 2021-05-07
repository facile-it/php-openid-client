<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\Runner;

use Facile\OpenIDClient\ConformanceTest\RpTest\RpTestInterface;
use Facile\OpenIDClient\ConformanceTest\TestInfo;
use Throwable;

class RpTestResult
{
    /** @var RpTestInterface */
    private $test;

    /** @var TestInfo */
    private $testInfo;

    /** @var string */
    private $implementation;

    /** @var Throwable */
    private $exception;

    /**
     * RpTestResult constructor.
     */
    public function __construct(RpTestInterface $test, TestInfo $testInfo, string $implementation, ?Throwable $exception = null)
    {
        $this->test = $test;
        $this->testInfo = $testInfo;
        $this->implementation = $implementation;
        $this->exception = $exception;
    }

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
