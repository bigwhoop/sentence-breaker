<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\BoundaryProbabilityCalculation;

use Bigwhoop\SentenceBreaker\Lexing\Tokens\ExclamationPointToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\PeriodToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuestionMarkToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuotedStringToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WhitespaceToken;

class Calculator
{
    /** @var Token[]|string[] */
    private $tokens = [];

    /** @var int */
    private $currentIdx = 0;

    /** @var string[] */
    private $abbreviations = [];

    /**
     * @param Token[]|string[] $tokens
     *
     * @throws CalculatorException
     */
    private function setTokens(array $tokens)
    {
        if (count($tokens) < 2) {
            throw new CalculatorException('Need at least 2 tokens.');
        }

        $this->tokens = array_values($tokens);
    }

    /**
     * @param array $abbreviations
     */
    public function setAbbreviations(array $abbreviations)
    {
        $this->abbreviations = $this->normalizeAbbreviations($abbreviations);
    }

    /**
     * @param array $abbreviations
     *
     * @return array
     */
    private function normalizeAbbreviations(array $abbreviations)
    {
        return array_map(function ($abbreviation) {
            return rtrim($abbreviation, '.');
        }, $abbreviations);
    }

    /**
     * @param Token[]|string[] $tokens
     *
     * @return TokenProbability[]
     */
    public function calculate(array $tokens)
    {
        $this->setTokens($tokens);

        $probabilities = [];

        for ($this->currentIdx = 0, $c = count($this->tokens); $this->currentIdx < $c; $this->currentIdx++) {
            $probabilities[] = $this->calculateCurrentTokenProbability();
        }

        return $probabilities;
    }

    /**
     * @return TokenProbability
     */
    private function calculateCurrentTokenProbability()
    {
        $currentToken = $this->getToken();
        $prop = new TokenProbability($currentToken);

        if ($currentToken instanceof QuestionMarkToken || $currentToken instanceof ExclamationPointToken) {
            $prop->setProbability(100);
        } elseif ($currentToken instanceof PeriodToken) {
            $prevToken = $this->getToken(-1);
            if ($prevToken instanceof WhitespaceToken) {
                $prevToken = $this->getToken(-2);
            }

            if (is_string($prevToken)) {
                if (false !== strpos($prevToken, '.')) {
                    $nextToken = $this->getToken(+1);
                    if ($nextToken instanceof WhitespaceToken) {
                        $nextToken = $this->getToken(+2);
                    }

                    if (is_string($nextToken) && ctype_upper(substr($nextToken, 0, 1))) {
                        $prop->setProbability(60);
                    } else {
                        $prop->setProbability(25);
                    }
                } elseif (in_array($prevToken, $this->abbreviations)) {
                    $prop->setProbability(0);
                } else {
                    $prop->setProbability(75);
                }
            } else {
                $prop->setProbability(50);
            }
        } elseif ($currentToken instanceof QuotedStringToken) {
            $nextToken = $this->getToken(+1);
            if ($nextToken instanceof WhitespaceToken) {
                $nextToken = $this->getToken(+2);
            }

            if (is_string($nextToken) && ctype_upper(substr($nextToken, 0, 1))) {
                $prop->setProbability(80);
            } else {
                $prop->setProbability(40);
            }
        }

        return $prop;
    }

    /**
     * @param int $offset
     *
     * @return Token|null|string
     */
    private function getToken($offset = 0)
    {
        $idx = $this->currentIdx + $offset;

        if (!array_key_exists($idx, $this->tokens)) {
            return;
        }

        return $this->tokens[$idx];
    }
}
