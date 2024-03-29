<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;

abstract class State
{
    /**
     * @throws StateException
     */
    final public function __invoke(Lexer $lexer): ?State
    {
        return $this->call($lexer);
    }

    /**
     * @throws StateException
     */
    abstract protected function call(Lexer $lexer): ?State;
}
