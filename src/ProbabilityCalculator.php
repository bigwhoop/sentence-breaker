<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker;

use Bigwhoop\SentenceBreaker\Abbreviations\Abbreviations;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\AbbreviationToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\PotentialAbbreviationToken;
use Bigwhoop\SentenceBreaker\Rules\ConfigurationException;
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

    public function setAbbreviations(Abbreviations $abbreviations): void
    {
        $this->abbreviations = $abbreviations;
    }

    public function addAbbreviations(Abbreviations $abbreviations): void
    {
        $this->abbreviations->addAbbreviations($abbreviations->getAbbreviations());
    }

    public function setRules(Rules $rules): void
    {
        $this->rules = $rules;
    }

    public function addRules(Rules $rules): void
    {
        $this->rules->addRules($rules->getRules());
    }

    /**
     * @param Token[] $tokens
     * @return TokenProbability[]
     * @throws ConfigurationException
     */
    public function calculate(array $tokens): array
    {
        foreach ($tokens as $idx => $token) {
            if ($token instanceof PotentialAbbreviationToken && $this->abbreviations->hasAbbreviation($token->getValue())) {
                $tokens[$idx] = new AbbreviationToken($token->getValue());
            }
        }

        $probabilities = [];

        foreach ($tokens as $i => $token) {
            $probability = new TokenProbability($token);

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
