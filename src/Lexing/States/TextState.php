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
use Bigwhoop\SentenceBreaker\Lexing\Tokens\DoubleQuoteToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\EOFToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\ExclamationPointToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\PeriodToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuestionMarkToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuotedStringToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\SingleQuoteToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WordToken;

class TextState extends State
{
    /**
     * {@inheritdoc}
     */
    protected function call(Lexer $lexer)
    {
        while (true) {
            $peek = $lexer->peek();
            
            if ($peek === null) {
                $lexer->emit();
                
                return null;
            }
            
            if ('.' === $peek) {
                if (in_array($lexer->peek(1), [' ', null], true)) {
                    $lexer->emit();
                    $lexer->next();
                    $lexer->emit(new PeriodToken());
                } else {
                    $lexer->next();
                }
                
                continue;
            }
            
            if ('?' === $peek) {
                $lexer->emit();
                $lexer->next();
                $lexer->emit(new QuestionMarkToken());
                
                continue;
            }
            
            if ('!' === $peek) {
                $lexer->emit();
                $lexer->next();
                $lexer->emit(new ExclamationPointToken());
                
                continue;
            }
            
            if (in_array($peek, QuotedStringState::CHARS, true)) {
                $lexer->emit();
                
                return new QuotedStringState();
            }
            
            if (in_array($peek, WhitespaceState::CHARS, true)) {
                $lexer->emit();
                
                return new WhitespaceState();
            }
            
            $lexer->next();
        }
    }
}
