<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tests;

use Bigwhoop\SentenceBreaker\Abbreviations\Abbreviations;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\CapitalizedWordToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\EOFToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WordToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WhitespaceToken;
use Bigwhoop\SentenceBreaker\Rules\IniConfiguration;
use Bigwhoop\SentenceBreaker\ProbabilityCalculator;
use Bigwhoop\SentenceBreaker\Lexing\Lexer;

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
                ['T_PERIOD 75', 'T_ABBREVIATION 0', 'T_PERIOD 0', 'T_PERIOD 75'],
                ['Mr.'],
            ],
            [
                'Dr. Mr. Mrs. Foo',
                ['T_ABBREVIATION 0', 'T_PERIOD 0', 'T_ABBREVIATION 0', 'T_PERIOD 0', 'T_ABBREVIATION 0', 'T_PERIOD 0'],
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
     * @param string $input
     * @param array  $expectedResult
     * @param array  $abbreviations
     */
    private function runCalculateTest($input, array $expectedResult, array $abbreviations)
    {
        $lexer = new Lexer();
        $tokens = $lexer->run($input);

        $rules = IniConfiguration::loadFile(__DIR__.'/../../rules/rules.ini')->getRules();

        $calc = new ProbabilityCalculator($rules);
        $calc->setAbbreviations(new Abbreviations($abbreviations));

        $probabilities = $calc->calculate($tokens);

        $actual = [];
        foreach ($probabilities as $probability) {
            $token = $probability->getToken();

            if ($token instanceof WordToken || $token instanceof CapitalizedWordToken
                || $token instanceof WhitespaceToken || $token instanceof EOFToken) {
                continue;
            }

            $actual[] = $token->getName().' '.$probability->getProbability();
        }

        $this->assertEquals($expectedResult, $actual);
    }
}
