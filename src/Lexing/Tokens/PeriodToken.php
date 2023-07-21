<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class PeriodToken implements Token
{
    public function getName(): string
    {
        return 'T_PERIOD';
    }

    public function getPrintableValue(): string
    {
        return '.';
    }
}
