<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker;

use Bigwhoop\SentenceBreaker\Abbreviations\Abbreviations;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\AbbreviationToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\PotentialAbbreviationToken;
use Bigwhoop\SentenceBreaker\Rules\Rules;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;

class ProbabilityCalculator
{
    /** @var Abbreviations */
    private $abbreviations;

    /** @var Rules */
    private $rules;

    /**
     * @param Rules $rules
     */
    public function __construct(Rules $rules)
    {
        $this->rules = $rules;
        $this->abbreviations = new Abbreviations();
    }

    /**
     * @param Abbreviations $abbreviations
     */
    public function setAbbreviations(Abbreviations $abbreviations)
    {
        $this->abbreviations = $abbreviations;
    }

    /**
     * @param Abbreviations $abbreviations
     */
    public function addAbbreviations(Abbreviations $abbreviations)
    {
        $this->abbreviations->addAbbreviations($abbreviations->getAbbreviations());
    }

    /**
     * @param Rules $rules
     */
    public function setRules(Rules $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @param Rules $rules
     */
    public function addRules(Rules $rules)
    {
        $this->rules->addRules($rules->getRules());
    }

    /**
     * @param Token[] $tokens
     *
     * @return TokenProbability[]
     */
    public function calculate(array $tokens)
    {
        foreach ($tokens as $idx => $token) {
            if ($token instanceof PotentialAbbreviationToken) {
                if ($this->abbreviations->hasAbbreviation($token->getValue())) {
                    $tokens[$idx] = new AbbreviationToken($token->getValue());
                }
            }
        }

        $probabilities = [];

        for ($i = 0, $c = count($tokens); $i < $c; $i++) {
            $token = $tokens[$i];
            $probability = new TokenProbability($token, 0);

            if ($this->rules->hasRule($token->getName())) {
                $patterns = $this->rules->getRule($token->getName())->getPatterns();

                foreach ($patterns as $pattern) {
                    $offsets = $pattern->getTokensOffsetRelativeToStartToken($token->getName());

                    foreach ($offsets as $offset => $expectedToken) {
                        if (!array_key_exists($i + $offset, $tokens)) {
                            continue 2;
                        }

                        $actualToken = $tokens[$i + $offset];

                        if ($actualToken->getName() !== $expectedToken->getTokenName()) {
                            continue 2;
                        }
                    }

                    $probability->setProbability($pattern->getProbability());
                }
            }

            $probabilities[] = $probability;
        }

        return $probabilities;
    }
}
