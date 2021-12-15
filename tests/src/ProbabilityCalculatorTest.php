<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Tests;

use Bigwhoop\SentenceBreaker\Abbreviations\Abbreviations;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\CapitalizedWordToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\EOFToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WordToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WhitespaceToken;
use Bigwhoop\SentenceBreaker\Rules\IniConfiguration;
use Bigwhoop\SentenceBreaker\ProbabilityCalculator;
use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use PHPUnit\Framework\TestCase;

class ProbabilityCalculatorTest extends TestCase
{
    /**
     * @return array<mixed>
     */
    public function dataSimpleSentences(): array
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
     * @return array<mixed>
     */
    public function dataQuotes(): array
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
     * @return array<mixed>
     */
    public function dataAbbreviations(): array
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
     * @param array<string>  $expectedResult
     */
    public function testSimpleSentences(string $input, array $expectedResult): void
    {
        $this->runCalculateTest($input, $expectedResult, []);
    }

    /**
     * @dataProvider dataQuotes
     *
     * @param string $input
     * @param array<string>  $expectedResult
     */
    public function testQuotes(string $input, array $expectedResult): void
    {
        $this->runCalculateTest($input, $expectedResult, []);
    }

    /**
     * @dataProvider dataAbbreviations
     *
     * @param string $input
     * @param array<string>  $expectedResult
     * @param array<string>  $abbreviations
     */
    public function testAbbreviations(string $input, array $expectedResult, array $abbreviations): void
    {
        $this->runCalculateTest($input, $expectedResult, $abbreviations);
    }

    /**
     * @param string $input
     * @param array<string>  $expectedResult
     * @param array<string>  $abbreviations
     */
    private function runCalculateTest(string $input, array $expectedResult, array $abbreviations): void
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
