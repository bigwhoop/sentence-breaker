<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class QuotedStringToken extends ValueToken
{
    public function getName(): string
    {
        return 'T_QUOTED_STR';
    }
}
