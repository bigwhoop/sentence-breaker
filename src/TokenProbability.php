<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker;

use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;

class TokenProbability
{
    private Token $token;

    private int $probability = 0;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function getToken(): Token
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
