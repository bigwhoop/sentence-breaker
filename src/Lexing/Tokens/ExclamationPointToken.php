<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class ExclamationPointToken implements Token
{
    public function getName(): string
    {
        return 'T_EXCLAMATION_POINT';
    }

    public function getPrintableValue(): string
    {
        return '!';
    }
}
