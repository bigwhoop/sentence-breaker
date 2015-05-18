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
    /**
     * @return array
     */
    public function dataSimpleSentences()
    {
        return [
            [
                'Sentence one. Sentence two? Sentence 3! Oh, yeah! Is this it? Yes.',
                ['Sentence one.', 'Sentence two?', 'Sentence 3!', 'Oh, yeah!', 'Is this it?', 'Yes.'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataQuotes()
    {
        return [
            [
                'He said: "Today is a good day." I replied: \'Is it?\'',
                ['He said: "Today is a good day."', "I replied: 'Is it?'"],
            ],
            [
                'He said: "Tom Jones is here!" and I believed him.',
                ['He said: "Tom Jones is here!" and I believed him.'],
            ],
            [
                'He said: "Tom Jones is here!", and I believed him.',
                ['He said: "Tom Jones is here!", and I believed him.'],
            ],
            [
                'He said: "Tom \'Tommy\' Jones is here!" And I believed him.',
                ['He said: "Tom \'Tommy\' Jones is here!"', 'And I believed him.'],
            ],
        ];
    }

    /**
     * @return array
     */
    public function dataAbbreviations()
    {
        return [
            [
                'Dr. Mr. Mrs.',
                ['Dr.', 'Mr.', 'Mrs.'],
                [],
            ],
            [
                'Dr. Mr. Mrs.',
                ['Dr.', 'Mr. Mrs.'],
                ['Mr.'],
            ],
            [
                'Dr. Mr. Mrs.',
                ['Dr. Mr. Mrs.'],
                ['Dr', 'Mr', 'Mrs.'],
            ],
        ];
    }

    /**
     * @dataProvider dataSimpleSentences
     *
     * @param string $input
     * @param string $expectedResult
     */
    public function testSimpleSentences($input, $expectedResult)
    {
        $this->runCalculateTest($input, $expectedResult, []);
    }

    /**
     * @dataProvider dataQuotes
     *
     * @param string $input
     * @param string $expectedResult
     */
    public function testQuotes($input, $expectedResult)
    {
        $this->runCalculateTest($input, $expectedResult, []);
    }

    /**
     * @dataProvider dataAbbreviations
     *
     * @param string $input
     * @param string $expectedResult
     * @param array  $abbreviations
     */
    public function testAbbreviations($input, $expectedResult, array $abbreviations)
    {
        $this->runCalculateTest($input, $expectedResult, $abbreviations);
    }

    /**
     * @param string $input
     * @param string $expectedResult
     * @param array  $abbreviations
     */
    private function runCalculateTest($input, $expectedResult, array $abbreviations)
    {
        $lexer = new Lexer();
        $tokens = $lexer->run($input);

        $calc = new Calculator();
        $calc->setAbbreviations($abbreviations);
        $actual = $calc->calculate($tokens);

        $this->assertEquals($expectedResult, $actual);
    }
}
