<?php
/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tests\Lexing;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    public function testLexer()
    {
        $text = <<<TXT
This is sentence one. Sentence two! Sentence three? Sentence "four". Sentence "five"! Sentence "six"? Sentence
"seven." Sentence 'eight!' Dr. Jones said: "Mrs. Smith you have a lovely daughter!" The T.V.A. is a big project!
TXT;
        
        $lexer = new Lexer($text);
        $lexer = new Lexer('He said: "Hello there!"');
        $lexer->run();
        
        // He said: "Hello there!"
        // "He" SPACE "said" COLON SPACE DBL_QUOTE "Hello" SPACE "there" EXCLAMATION_POINT DBL_QUOTE
    }
}
