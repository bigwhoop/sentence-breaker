<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Rules;

class RulePattern
{
    private int $probability;

    /** @var array<int, RulePatternToken> */
    private array $tokens = [];

    /**
     * @param array<int, RulePatternToken> $tokens
     */
    public function __construct(int $probability, array $tokens = [])
    {
        $this->probability = $probability;
        $this->addTokens($tokens);
    }

    public function getProbability(): int
    {
        return $this->probability;
    }

    /**
     * @param RulePatternToken[] $tokens
     */
    public function addTokens(array $tokens): void
    {
        foreach ($tokens as $token) {
            $this->addToken($token);
        }
    }

    public function addToken(RulePatternToken $token): void
    {
        $this->tokens[] = $token;
    }

    /**
     * Returns the offset for each token relative to the start token.
     *
     * Let's say we have the following tokens: T_A, T_B, T_C, T_D
     * If the $startTokenName were T_A we'd return: 0, 1, 2, 3
     * If the $startTokenName were T_C we'd return: -2, -1, 0, 1
     *
     * @return RulePatternToken[]
     *
     * @throws ConfigurationException
     */
    public function getTokensOffsetRelativeToStartToken(string $startTokenName): array
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
            throw new ConfigurationException('No start token found for pattern ' . print_r($this, true));
        }

        $offsets = array_map(
            static fn (int $idx) => $idx - $startTokenIdx,
            range(0, count($this->tokens) - 1)
        );

        /* @phpstan-ignore-next-line */
        return array_combine($offsets, $this->tokens);
    }
}
