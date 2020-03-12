<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class WhitespaceToken extends ValueToken
{
    public function getName(): string
    {
        return 'T_WHITESPACE';
    }
}
