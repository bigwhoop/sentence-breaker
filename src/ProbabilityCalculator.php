<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker;

use Bigwhoop\SentenceBreaker\Abbreviations\Abbreviations;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\AbbreviationToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\PotentialAbbreviationToken;
use Bigwhoop\SentenceBreaker\Rules\ConfigurationException;
use Bigwhoop\SentenceBreaker\Rules\Rules;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;
use Generator;

class ProbabilityCalculator
{
    private Abbreviations $abbreviations;

    private Rules $rules;

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
     * @param iterable<Token> $tokens
     * @return Generator<TokenProbability>
     * @throws ConfigurationException
     */
    public function calculate(iterable $tokens): Generator
    {
        $tokenGenerator = function () use ($tokens) {
            foreach ($tokens as $token) {
                if ($token instanceof PotentialAbbreviationToken && $this->abbreviations->hasAbbreviation(
                    $token->getValue()
                )) {
                    $token = new AbbreviationToken($token->getValue());
                }

                yield $token;
            }
        };

        $iterator = new GeneratorOffsetIterator($tokenGenerator());

        foreach ($iterator as $token) {
            $probability = new TokenProbability($token);
            if ($this->rules->hasRule($token->getName())) {
                $patterns = $this->rules->getRule($token->getName())->getPatterns();

                foreach ($patterns as $pattern) {
                    $offsets = $pattern->getTokensOffsetRelativeToStartToken($token->getName());
                    foreach ($offsets as $offset => $expectedToken) {
                        if ($iterator->getByOffset($offset) === false) {
                            continue 2;
                        }

                        $actualToken = $iterator->getByOffset($offset);
                        if ($actualToken->getName() !== $expectedToken->getTokenName()) {
                            continue 2;
                        }
                    }

                    $probability->setProbability($pattern->getProbability());
                }
            }

            yield $probability;
        }
    }
}
