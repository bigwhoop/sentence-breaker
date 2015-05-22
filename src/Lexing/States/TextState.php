<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\EOFToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\ExclamationPointToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\PeriodToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuestionMarkToken;

class TextState extends State
{
    /**
     * {@inheritdoc}
     */
    protected function call(Lexer $lexer)
    {
        while (true) {
            $peek = $lexer->peek();
            //file_put_contents(__DIR__ . '/foo.log', '#' . $lexer->pos() . ' ' . $peek . ' (' . $lexer->getTokenValue() . ')' . PHP_EOL, FILE_APPEND);
            if ($peek === null) {
                $lexer->emit(new EOFToken());

                return;
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

            if (in_array($peek, QuotedStringState::CHARS, true)) {
                return new QuotedStringState();
            }

            if (in_array($peek, WhitespaceState::CHARS, true)) {
                return new WhitespaceState();
            }

            return new WordState();
        }
    }
}
