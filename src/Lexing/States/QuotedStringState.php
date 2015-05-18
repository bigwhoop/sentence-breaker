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

class QuotedStringState extends State
{
    const CHARS = ['"', "'"];
    
    /**
     * {@inheritdoc}
     */
    protected function call(Lexer $lexer)
    {
        $start = $lexer->next();
        
        while ($start !== $lexer->next()) {
            // ...
        }
        
        $lexer->emit(new QuotedStringToken($lexer->getTokenValue()));
        
        return new TextState();
    }
}
