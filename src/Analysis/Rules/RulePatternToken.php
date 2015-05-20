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

class RulePatternToken
{
    /** @var string */
    private $tokenClass = '';

    /** @var bool */
    private $isStartToken = false;

    /**
     * @param string $tokenClass
     * @param bool   $isStartToken
     */
    public function __construct($tokenClass, $isStartToken = false)
    {
        $this->tokenClass = $tokenClass;
        $this->isStartToken = $isStartToken;
    }

    /**
     * @return string
     */
    public function getTokenClass()
    {
        return $this->tokenClass;
    }

    /**
     * @return bool
     */
    public function isStartToken()
    {
        return $this->isStartToken;
    }
}
