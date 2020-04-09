<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\RpTest;

use Facile\OpenIDClient\ConformanceTest\TestInfo;

interface RpTestInterface
{
    public function getTestId(): string;

    public function execute(TestInfo $testInfo): void;
}
