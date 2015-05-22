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

class RulePatternToken
{
    /** @var string */
    private $tokenName = '';

    /** @var bool */
    private $isStartToken = false;

    /**
     * @param string $tokenName
     * @param bool   $isStartToken
     */
    public function __construct($tokenName, $isStartToken = false)
    {
        $this->tokenName = $tokenName;
        $this->isStartToken = $isStartToken;
    }

    /**
     * @return string
     */
    public function getTokenName()
    {
        return $this->tokenName;
    }

    /**
     * @return bool
     */
    public function isStartToken()
    {
        return $this->isStartToken;
    }
}
