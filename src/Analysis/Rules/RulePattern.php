<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Analysis\Rules;

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
}
