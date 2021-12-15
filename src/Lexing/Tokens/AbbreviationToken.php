<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class AbbreviationToken extends ValueToken
{
    public function getName(): string
    {
        return 'T_ABBREVIATION';
    }
}
