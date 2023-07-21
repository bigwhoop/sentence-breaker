<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\EOFToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\ExclamationPointToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\PeriodToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuestionMarkToken;

class TextState extends State
{
    protected function call(Lexer $lexer): ?State
    {
        while (true) {
            $peek = $lexer->peek();

            if ($peek === null) {
                $lexer->emit(new EOFToken());

                return null;
            }

            if ('.' === $peek) {
                $lexer->next();
                $lexer->emit(new PeriodToken());

                continue;
            }

            if ('?' === $peek) {
                $lexer->next();
                $lexer->emit(new QuestionMarkToken());

                continue;
            }

            if ('!' === $peek) {
                $lexer->next();
                $lexer->emit(new ExclamationPointToken());

                continue;
            }

            if (in_array($peek, WhitespaceState::CHARS, true)) {
                return new WhitespaceState();
            }

            if ('.' !== $lexer->last() && in_array($peek, QuotedStringState::getLeftMarks(), true)) {
                return new QuotedStringState();
            }

            if (in_array($peek, ParenthesesState::getOpeningParentheses(), true)) {
                return new ParenthesesState();
            }

            return new WordState();
        }
    }
}
