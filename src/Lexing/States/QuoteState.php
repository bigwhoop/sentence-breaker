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

class QuoteState extends State
{
    
    
    /**
     * {@inheritdoc}
     */
    protected function call(Lexer $lexer)
    {
        $start = $lexer->next();
        
        if ($start !== '"' && $start !== "'") {
            $lexer->error("Quotes must start with either %s or %s. Got %s", "'", '"', $start);
        }
        
        while (($c = $lexer->next())) {
            if ($c === $start) {
                $lexer->emit(new QuotedStringToken());
                
                return new TextState();
            }
        }
    }
}
