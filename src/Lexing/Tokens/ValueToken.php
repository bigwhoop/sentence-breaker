<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

abstract class ValueToken implements Token
{
    private string $value;

    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getPrintableValue(): string
    {
        return $this->getValue();
    }
}
