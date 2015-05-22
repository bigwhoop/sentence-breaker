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
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WhitespaceToken;

class WhitespaceState extends State
{
    const CHARS = [' ', "\t", "\r", "\n"];

    /**
     * {@inheritdoc}
     */
    protected function call(Lexer $lexer)
    {
        while (in_array($lexer->peek(), self::CHARS, true)) {
            $lexer->next();
        }

        $lexer->emit(new WhitespaceToken());

        return new TextState();
    }
}
