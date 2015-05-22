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

class Rule
{
    /** @var string */
    private $tokenName = '';

    /** @var RulePattern[] */
    private $patterns = [];

    /**
     * @param string        $tokenName
     * @param RulePattern[] $patterns
     */
    public function __construct($tokenName, array $patterns = [])
    {
        $this->tokenName = $tokenName;
        $this->addPatterns($patterns);
    }

    /**
     * @return string
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }

    /**
     * @param RulePattern[] $patterns
     */
    public function addPatterns(array $patterns)
    {
        foreach ($patterns as $pattern) {
            $this->addPattern($pattern);
        }
    }

    /**
     * @param RulePattern $pattern
     */
    public function addPattern(RulePattern $pattern)
    {
        $this->patterns[] = $pattern;
    }

    /**
     * @return RulePattern[]
     */
    public function getPatterns()
    {
        return $this->patterns;
    }
}
