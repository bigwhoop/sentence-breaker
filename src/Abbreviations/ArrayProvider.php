<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Abbreviations;

class ArrayProvider implements ValueProvider
{
    private $values = [];

    public function __construct(array $values)
    {
        $this->values = $values;
    }
    
    public function getValues(): array
    {
        return $this->values;
    }
}
