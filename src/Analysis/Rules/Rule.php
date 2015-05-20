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

class Rule
{
    /** @var string */
    private $tokenClass = '';

    /** @var RulePattern[] */
    private $patterns = [];

    /**
     * @param string        $tokenClass
     * @param RulePattern[] $patterns
     */
    public function __construct($tokenClass, array $patterns = [])
    {
        $this->tokenClass = $tokenClass;
        $this->addPatterns($patterns);
    }

    /**
     * @return string
     */
    public function getTokenClass()
    {
        return $this->tokenClass;
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
