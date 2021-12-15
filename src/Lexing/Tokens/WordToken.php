<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class WordToken extends ValueToken implements PotentialAbbreviationToken
{
    public function getName(): string
    {
        return 'T_WORD';
    }
}
