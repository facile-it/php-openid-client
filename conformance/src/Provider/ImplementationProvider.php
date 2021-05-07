<?php

declare(strict_types=1);

namespace Facile\OpenIDClient\ConformanceTest\Provider;

use function array_map;
use function array_pop;
use function array_shift;
use function array_slice;
use function array_unshift;
use Closure;
use function file;
use function implode;
use function is_callable;
use function preg_match;
use function preg_replace;
use ReflectionFunction;
use function sprintf;
use function str_repeat;
use function strlen;

class ImplementationProvider
{
    /** @var int */
    private $indent;

    /**
     * ImplementationProvider constructor.
     */
    public function __construct(int $indent = 4)
    {
        $this->indent = $indent;
    }

    public function getCallableCode(callable $closure)
    {
        if (is_callable($closure)) {
            $closure = Closure::fromCallable($closure);
        }

        $r = new ReflectionFunction($closure);
        $lines = file($r->getFileName());
        $lines = array_slice($lines, $r->getStartLine(), $r->getEndLine() - $r->getStartLine());
        if (preg_match('/^ *{ *$/', $lines[0] ?? '')) {
            unset($lines[0]);
        }

        $firstLine = array_shift($lines) ?: '';

        if (! preg_match('/^ *{ *$/', $firstLine)) {
            array_unshift($lines, $firstLine);
        }

        $lastLine = array_pop($lines) ?: '';
        if (! preg_match('/^ *} *$/', $lastLine)) {
            $lines[] = $lastLine;
        }

        // remove spaces based on first line
        if (preg_match('/^( +)/', $lines[0] ?? '', $matches)) {
            $toTrim = strlen($matches[1]);
            $lines = array_map(static function (string $line) use ($toTrim) {
                return preg_replace(sprintf('/^ {0,%d}/', $toTrim), '', $line);
            }, $lines);
        }

        if ($this->indent) {
            $lines = array_map(function (string $line) {
                return str_repeat(' ', $this->indent) . $line;
            }, $lines);
        }

        return implode('', $lines);
    }
}
