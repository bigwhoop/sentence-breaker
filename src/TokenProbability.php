<?php
declare(strict_types=1);

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

    public function setProbability(int $p): void
    {
        $this->probability = $p;
    }

    public function increaseProbability(int $p): void
    {
        $this->probability += $p;

        if ($this->probability > 100) {
            $this->probability = 100;
        }
    }

    public function decreaseProbability(int $p): void
    {
        $this->probability -= $p;

        if ($this->probability < 0) {
            $this->probability = 0;
        }
    }

    public function getProbability(): int
    {
        return $this->probability;
    }
}
