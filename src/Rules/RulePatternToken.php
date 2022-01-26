<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Rules;

class RulePatternToken
{
    private string $tokenName;

    private bool $isStartToken;

    public function __construct(string $tokenName, bool $isStartToken = false)
    {
        $this->tokenName = $tokenName;
        $this->isStartToken = $isStartToken;
    }

    public function getTokenName(): string
    {
        return $this->tokenName;
    }

    public function isStartToken(): bool
    {
        return $this->isStartToken;
    }
}
