<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;

abstract class State
{
    /**
     * @param Lexer $lexer
     * @return State|null
     * @throws StateException
     */
    final public function __invoke(Lexer $lexer): ?State
    {
        return $this->call($lexer);
    }

    /**
     * @param Lexer $lexer
     * @return State|null
     * @throws StateException
     */
    abstract protected function call(Lexer $lexer): ?State;
}
