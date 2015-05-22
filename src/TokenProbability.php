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

use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;

class TokenProbability
{
    /** @var Token|string */
    private $token;

    /** @var int */
    private $probability = 0;

    /**
     * @param Token|string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return Token|string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param int $p
     */
    public function setProbability($p)
    {
        $this->probability = $p;
    }

    /**
     * @param int $p
     */
    public function increaseProbability($p)
    {
        $this->probability += $p;

        if ($this->probability > 100) {
            $this->probability = 100;
        }
    }

    /**
     * @param int $p
     */
    public function decreaseProbability($p)
    {
        $this->probability -= $p;

        if ($this->probability < 0) {
            $this->probability = 0;
        }
    }

    /**
     * @return int
     */
    public function getProbability()
    {
        return $this->probability;
    }
}
