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
                if ($lexer->hasMoved()) {
                    $lexer->emit(new WordToken());
                }
                
                return null;
            }
            
            switch ($peek) {
                case '.':
                    if ($lexer->hasMoved()) {
                        $lexer->emit(new WordToken());
                    }
                    $lexer->next();
                    $lexer->emit(new PeriodToken());
                    break;
                
                case '!':
                    if ($lexer->hasMoved()) {
                        $lexer->emit(new WordToken());
                    }
                    $lexer->next();
                    $lexer->emit(new ExclamationPointToken());
                    break;
                
                case '?':
                    if ($lexer->hasMoved()) {
                        $lexer->emit(new WordToken());
                    }
                    $lexer->next();
                    $lexer->emit(new QuestionMarkToken());
                    break;
                
                case ' ':
                    if ($lexer->hasMoved()) {
                        $lexer->emit(new WordToken());
                    }
                    return new SpaceState();
                
                case '"':
                    if ($lexer->hasMoved()) {
                        $lexer->emit(new WordToken());
                    }
                    $lexer->next();
                    $lexer->emit(new DoubleQuoteToken());
                    break;
                
                case "'":
                    if ($lexer->hasMoved()) {
                        $lexer->emit(new WordToken());
                    }
                    $lexer->next();
                    $lexer->emit(new SingleQuoteToken());
                    break;
                
                default:
                    $lexer->next();
                    break;
            }
        }
    }
}
