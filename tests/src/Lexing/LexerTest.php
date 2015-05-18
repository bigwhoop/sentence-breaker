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
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    public function testCompleteSentence()
    {
        $text     = 'He said: "Hello there!" How are you? Good.';
        $expected = '"He" "said:" T_QUOTED_STR "How" "are" "you" T_QUERSTION_MARK "Good" T_PERIOD';
        
        $lexer = new Lexer($text);
        $tokens = $lexer->run();
        
        $actual = $this->getTokensString($tokens);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testAbbreviations()
    {
        $text = 'Hello Mr. Jones, please turn on the T.V.';
        $expected = '"Hello" "Mr" T_PERIOD "Jones," "please" "turn" "on" "the" "T.V" T_PERIOD';
        
        $lexer = new Lexer($text);
        $tokens = $lexer->run();
        
        $actual = $this->getTokensString($tokens);
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param Token[]|string[] $tokens
     * @return string
     */
    private function getTokensString(array $tokens)
    {
        $chunks = [];
        foreach ($tokens as $token) {
            $chunks[] = $token instanceof Token ? $token->getName() : '"' . $token . '"';
        }
        
        return join(' ', $chunks);
    }
}
