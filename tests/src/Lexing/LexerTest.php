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

use Bigwhoop\SentenceBreaker\Lexing\Item;
use Bigwhoop\SentenceBreaker\Lexing\Lexer;

class LexerTest extends \PHPUnit_Framework_TestCase
{
    public function testCompleteSentence()
    {
        $text     = 'He said: "Hello there!" How are you? Good.';
        $expected = '"He" SPACETOKEN "said:" SPACETOKEN DOUBLEQUOTETOKEN "Hello" SPACETOKEN "there" EXCLAMATIONPOINTTOKEN DOUBLEQUOTETOKEN SPACETOKEN "How" SPACETOKEN "are" SPACETOKEN "you" QUESTIONMARKTOKEN SPACETOKEN "Good" PERIODTOKEN EOFTOKEN';
        
        $lexer = new Lexer($text);
        $items = $lexer->run();
        $actual = $this->getItemsString($items);
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testAbbreviations()
    {
        $text = 'Hello Mr. Jones, please turn on the T.V.';
        $expected = '"Hello" SPACETOKEN "Mr" PERIODTOKEN SPACETOKEN "Jones," SPACETOKEN "please" SPACETOKEN "turn" SPACETOKEN "on" SPACETOKEN "the" SPACETOKEN "T" PERIODTOKEN "V" PERIODTOKEN EOFTOKEN';
        
        $lexer = new Lexer($text);
        $items = $lexer->run();
        $actual = $this->getItemsString($items);
        
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param Item[] $items
     * @return string
     */
    private function getItemsString(array $items)
    {
        $actual = [];
        foreach ($items as $item) {
            $actual[] = $item->toString();
        }
        
        return join(' ', $actual);
    }
}
