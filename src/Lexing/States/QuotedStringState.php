<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuotedStringToken;

class QuotedStringState extends State
{
    public const CHARS = ['"', "'"];

    protected function call(Lexer $lexer): ?State
    {
        $start = $lexer->next();

        while (true) {
            $next = $lexer->next();

            if ($next === null) {
                throw new StateException('Failed to find end of quote. Reached end of input. Read: '.$lexer->getTokenValue());
            }

            if ($start === $next) {
                break;
            }
        }

        $lexer->emit(new QuotedStringToken());

        return new TextState();
    }
}
