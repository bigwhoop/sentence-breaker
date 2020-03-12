<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Abbreviations;

interface ValueProvider
{
    /**
     * @return string[]
     */
    public function getValues(): array;
}
