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
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuotedStringToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WordToken;

class TextState extends State
{
    /**
     * {@inheritdoc}
     */
    protected function call(Lexer $lexer)
    {
        while (true) {
            $next = $lexer->peek();
            
            if ($next === '"' || $next === "'") {
                return new QuoteState();
            }
            
            if ($next === ' ') {
                return new SpaceState();
            }
            
            return new WordState();
        }
    }
}
