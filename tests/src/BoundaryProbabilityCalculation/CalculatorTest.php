<?php
/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tests\BoundaryProbabilityCalculation;

use Bigwhoop\SentenceBreaker\BoundaryProbabilityCalculation\Calculator;
use Bigwhoop\SentenceBreaker\Lexing\Lexer;

class CalculatorTest extends \PHPUnit_Framework_TestCase
{
    public function testProbability()
    {
        $lexer = new Lexer('Hello Mr. Jones. Please turn on the T.V. thank you very much!');
        $items = $lexer->run();
        
        $calc = new Calculator();
        $calc->calculate($items);
    }
}
