<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tests\SentenceBoundary;

use Bigwhoop\SentenceBreaker\SentenceBoundary\Rules\XMLConfiguration;
use Bigwhoop\SentenceBreaker\SentenceBoundary\ProbabilityCalculator;
use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WhitespaceToken;

class ProbabilityCalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function dataSimpleSentences()
    {
        return [
            [
                'Sentence one. Sentence two? Sentence 3! Oh, yeah! Is this it? Yes.',
                [
                    'T_PERIOD 75',
                    'T_QUESTION_MARK 100',
                    'T_EXCLAMATION_POINT 100',
                    'T_EXCLAMATION_POINT 100',
                    'T_QUESTION_MARK 100',
                    'T_PERIOD 100',
                ],
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
                ['T_QUOTED_STR 75', 'T_QUOTED_STR 100'],
            ],
            [
                'He said: "Tom Jones is here!" and I believed him.',
                ['T_QUOTED_STR 25', 'T_PERIOD 100'],
            ],
            [
                'He said: "Tom Jones is here!", and I believed him.',
                ['T_QUOTED_STR 25', 'T_PERIOD 100'],
            ],
            [
                'He said: "Tom \'Tommy\' Jones is here!" And I believed him.',
                ['T_QUOTED_STR 75', 'T_PERIOD 100'],
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
                'Dr. Mr. Mrs. Foo',
                ['T_PERIOD 75', 'T_PERIOD 75', 'T_PERIOD 75'],
                [],
            ],
            [
                'Dr. Mr. Mrs. Foo',
                ['T_PERIOD 75', 'T_PERIOD 0', 'T_PERIOD 75'],
                ['Mr.'],
            ],
            [
                'Dr. Mr. Mrs. Foo',
                ['T_PERIOD 0', 'T_PERIOD 0', 'T_PERIOD 0'],
                ['Dr', 'Mr', 'Mrs.'],
            ],
        ];
    }

    /**
     * @dataProvider dataSimpleSentences
     *
     * @param string $input
     * @param array  $expectedResult
     */
    public function testSimpleSentences($input, array $expectedResult)
    {
        $this->runCalculateTest($input, $expectedResult, []);
    }

    /**
     * @dataProvider dataQuotes
     *
     * @param string $input
     * @param array  $expectedResult
     */
    public function testQuotes($input, array $expectedResult)
    {
        $this->runCalculateTest($input, $expectedResult, []);
    }

    /**
     * @dataProvider dataAbbreviations
     *
     * @param string $input
     * @param array  $expectedResult
     * @param array  $abbreviations
     */
    public function testAbbreviations($input, array $expectedResult, array $abbreviations)
    {
        $this->runCalculateTest($input, $expectedResult, $abbreviations);
    }

    /**
     * @expectedException \Bigwhoop\SentenceBreaker\SentenceBoundary\SentenceBoundaryException
     * @expectedExceptionMessage Need at least 2 tokens.
     */
    public function testNotEnoughTokens()
    {
        $this->runCalculateTest('Hi', [], []);
    }

    /**
     * @param string $input
     * @param array  $expectedResult
     * @param array  $abbreviations
     */
    private function runCalculateTest($input, array $expectedResult, array $abbreviations)
    {
        $lexer = new Lexer();
        $tokens = $lexer->run($input);

        $rules = XMLConfiguration::loadFile(__DIR__.'/../../../rules/rules.xml')->getRules();

        $calc = new ProbabilityCalculator($rules);
        $calc->setAbbreviations($abbreviations);

        $probabilities = $calc->calculate($tokens);

        $actual = [];
        foreach ($probabilities as $probability) {
            $token = $probability->getToken();
            if ($token instanceof Token && !($token instanceof WhitespaceToken)) {
                $actual[] = $token->getName().' '.$probability->getProbability();
            }
        }

        $this->assertEquals($expectedResult, $actual);
    }
}
