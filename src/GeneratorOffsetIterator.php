<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker;

use Iterator;

/**
 * @template T
 *
 * @implements Iterator<T>
 */
class GeneratorOffsetIterator implements \Iterator
{
    /** @var \Generator<int, T, mixed, void> */
    private \Generator $generator;

    /** @var array<T> */
    private array $cache = [];

    private int $currentIndex = 0;

    /**
     * @param \Generator<int, T, mixed, void> $generator
     */
    public function __construct(\Generator $generator)
    {
        $this->generator = $generator;
        $this->addCurrentToCache();
    }

    /**
     * @return T
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->cache[$this->currentIndex];
    }

    public function next(): void
    {
        if ($this->currentIndex === $this->generator->key()) {
            $this->generator->next();
            $this->addCurrentToCache();
        }

        ++$this->currentIndex;
    }

    public function key(): int
    {
        return $this->currentIndex;
    }

    public function valid(): bool
    {
        return array_key_exists($this->currentIndex, $this->cache);
    }

    public function rewind(): void
    {
        $this->currentIndex = 0;
    }

    private function addCurrentToCache(): void
    {
        if ($this->generator->valid()) {
            $this->cache[] = $this->generator->current();
        }
    }

    /**
     * @return false|T
     */
    public function getByOffset(int $offset)
    {
        if ($offset === 0) {
            return $this->current();
        }

        if ($offset < 0) {
            return $this->cache[$this->currentIndex - abs($offset)] ?? false;
        }

        $currentIndex = $this->currentIndex;

        // look ahead
        foreach (range(1, $offset) as $ignored) {
            if (!$this->valid()) {
                break;
            }
            $this->next();
        }

        if ($this->valid()) {
            $current = $this->current();
        } else {
            $current = false;
        }

        // reset current index
        $this->currentIndex = $currentIndex;

        return $current;
    }
}
