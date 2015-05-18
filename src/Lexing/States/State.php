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

abstract class State
{
    /**
     * @param Lexer $lexer
     *
     * @return State
     */
    final public function __invoke(Lexer $lexer)
    {
        return $this->call($lexer);
    }

    /**
     * @param Lexer $lexer
     *
     * @return State
     */
    abstract protected function call(Lexer $lexer);
}
