<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Rules;

class RulePattern
{
    /** @var int */
    private $probability = 0;

    /** @var RulePatternToken[] */
    private $tokens = [];

    /**
     * @param int                $probability
     * @param RulePatternToken[] $tokens
     */
    public function __construct($probability, array $tokens = [])
    {
        $this->probability = $probability;
        $this->addTokens($tokens);
    }

    /**
     * @return int
     */
    public function getProbability()
    {
        return $this->probability;
    }

    /**
     * @param RulePatternToken[] $tokens
     */
    public function addTokens(array $tokens)
    {
        foreach ($tokens as $token) {
            $this->addToken($token);
        }
    }

    /**
     * @param RulePatternToken $token
     */
    public function addToken(RulePatternToken $token)
    {
        $this->tokens[] = $token;
    }

    /**
     * @return RulePatternToken[]
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Returns the offset for each token relative to the start token.
     *
     * Let's say we have the following tokens: T_A, T_B, T_C, T_D
     * If the $startTokenName were T_A we'd return: 0, 1, 2, 3
     * If the $startTokenName were T_C we'd return: -2, -1, 0, 1
     *
     * @param string $startTokenName
     *
     * @return RulePatternToken[]
     *
     * @throws ConfigurationException
     */
    public function getTokensOffsetRelativeToStartToken($startTokenName)
    {
        $startTokenIdx = null;

        foreach ($this->tokens as $idx => $token) {
            if ($token->getTokenName() === $startTokenName) {
                $startTokenIdx = $idx;
            } elseif ($token->isStartToken()) {
                $startTokenIdx = $idx;
            }
        }

        if ($startTokenIdx === null) {
            throw new ConfigurationException('No start token found for pattern '.print_r($this, true));
        }

        $numTokens = count($this->tokens);

        $offsets = array_map(function ($idx) use ($startTokenIdx) {
            return $idx - $startTokenIdx;
        }, range(0, $numTokens - 1));

        return array_combine($offsets, $this->tokens);
    }
}
