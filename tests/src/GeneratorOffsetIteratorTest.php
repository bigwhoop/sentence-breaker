<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Tests;

use Bigwhoop\SentenceBreaker\GeneratorOffsetIterator;
use PHPUnit\Framework\TestCase;

class GeneratorOffsetIteratorTest extends TestCase
{
    public function testCanAccessPreviousAndNextValuesByOffset(): void
    {
        $generatorInvocationCount = 0;
        $yieldSequence = function () use (&$generatorInvocationCount) {
            $generatorInvocationCount++;
            yield from range(100, 110);
        };

        $iterator = new GeneratorOffsetIterator($yieldSequence());

        $value = null;
        foreach ($iterator as $index => $value) {
            if ($index === 5) {
                self::assertSame(105, $value);
                self::assertSame(105, $iterator->getByOffset(0));
                self::assertSame(103, $iterator->getByOffset(-2));
                self::assertSame(107, $iterator->getByOffset(2));
                self::assertFalse($iterator->getByOffset(-99));
                self::assertSame(110, $iterator->getByOffset(5));
                self::assertFalse($iterator->getByOffset(1123));
            }
        }

        self::assertSame(110, $value);
        self::assertSame(1, $generatorInvocationCount);
    }
}
