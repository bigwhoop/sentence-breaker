<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Rules;

class Rule
{
    /** @var string */
    private $tokenName;

    /** @var RulePattern[] */
    private $patterns = [];

    /**
     * @param string        $tokenName
     * @param RulePattern[] $patterns
     */
    public function __construct(string $tokenName, array $patterns = [])
    {
        $this->tokenName = $tokenName;
        $this->addPatterns($patterns);
    }

    public function getTokenName(): string
    {
        return $this->tokenName;
    }

    /**
     * @param RulePattern[] $patterns
     */
    public function addPatterns(array $patterns): void
    {
        foreach ($patterns as $pattern) {
            $this->addPattern($pattern);
        }
    }

    public function addPattern(RulePattern $pattern): void
    {
        $this->patterns[] = $pattern;
    }

    /**
     * @return RulePattern[]
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }
}
