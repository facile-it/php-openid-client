<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\Runner;

use Throwable;
use Facile\OpenIDClient\ConformanceTest\RpTest\RpTestInterface;
use Facile\OpenIDClient\ConformanceTest\TestInfo;

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
     * @param RpTestInterface $test
     * @param TestInfo $testInfo
     * @param string $implementation
     * @param Throwable|null $exception
     */
    public function __construct(RpTestInterface $test, TestInfo $testInfo, string $implementation, ?Throwable $exception = null)
    {
        $this->test = $test;
        $this->testInfo = $testInfo;
        $this->implementation = $implementation;
        $this->exception = $exception;
    }

    /**
     * @return RpTestInterface
     */
    public function getTest(): RpTestInterface
    {
        return $this->test;
    }

    /**
     * @return TestInfo
     */
    public function getTestInfo(): TestInfo
    {
        return $this->testInfo;
    }

    /**
     * @return string
     */
    public function getImplementation(): string
    {
        return $this->implementation;
    }

    /**
     * @return null|Throwable
     */
    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    /**
     * @param Throwable $exception
     */
    public function setException(Throwable $exception): void
    {
        $this->exception = $exception;
    }
}
