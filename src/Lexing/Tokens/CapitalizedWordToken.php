<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class CapitalizedWordToken extends ValueToken implements PotentialAbbreviationToken
{
    public function getName(): string
    {
        return 'T_CAPITALIZED_WORD';
    }
}
