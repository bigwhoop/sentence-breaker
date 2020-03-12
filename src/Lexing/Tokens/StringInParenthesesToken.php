<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class StringInParenthesesToken extends ValueToken
{
    public function getName(): string
    {
        return 'T_PARENTHESES_STR';
    }
}
