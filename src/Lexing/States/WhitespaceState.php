<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WhitespaceToken;

class WhitespaceState extends State
{
    public const CHARS = [' ', "\t", "\r", "\n"];

    protected function call(Lexer $lexer): ?State
    {
        while (in_array($lexer->peek(), self::CHARS, true)) {
            $lexer->next();
        }

        $lexer->emit(new WhitespaceToken());

        return new TextState();
    }
}
