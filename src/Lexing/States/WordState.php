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
use Bigwhoop\SentenceBreaker\Lexing\Tokens\CapitalizedWordToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WordToken;

class WordState extends State
{
    /**
     * @return array
     */
    private function getNonWordChars()
    {
        return array_merge(['.', '?', '!', null], WhitespaceState::CHARS);
    }

    /**
     * {@inheritdoc}
     */
    protected function call(Lexer $lexer)
    {
        $nonWordChars = $this->getNonWordChars();

        while (!in_array($lexer->peek(), $nonWordChars, true)) {
            $lexer->next();
        }

        $value = $lexer->getTokenValue();
        $firstChar = substr($value, 0, 1);

        if (ctype_upper($firstChar)) {
            $lexer->emit(new CapitalizedWordToken());
        } else {
            $lexer->emit(new WordToken());
        }

        return new TextState();
    }
}
