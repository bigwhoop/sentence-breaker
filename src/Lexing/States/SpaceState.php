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
use Bigwhoop\SentenceBreaker\Lexing\Tokens\SpaceToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WordToken;

class SpaceState extends State
{
    /**
     * {@inheritdoc}
     */
    protected function call(Lexer $lexer)
    {
        while ($lexer->peek() === ' ') {
            $lexer->next();
        }
        
        $lexer->emit(new SpaceToken());
        
        return new TextState();
    }
}
