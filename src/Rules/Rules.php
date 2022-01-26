<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Rules;

class Rules
{
    /** @var Rule[] */
    private array $rules = [];

    /**
     * @param Rule[] $rules
     */
    public function __construct(array $rules = [])
    {
        $this->addRules($rules);
    }

    /**
     * @param Rule[] $rules
     */
    public function addRules(array $rules): void
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    public function addRule(Rule $rule): void
    {
        if (array_key_exists($rule->getTokenName(), $this->rules)) {
            $this->rules[$rule->getTokenName()]->addPatterns($rule->getPatterns());
        } else {
            $this->rules[$rule->getTokenName()] = $rule;
        }
    }

    /**
     * @return Rule[]
     */
    public function getRules(): array
    {
        return array_values($this->rules);
    }

    public function hasRule(string $tokenName): bool
    {
        return array_key_exists($tokenName, $this->rules);
    }

    /**
     * @throws ConfigurationException
     */
    public function getRule(string $tokenName): Rule
    {
        if (!array_key_exists($tokenName, $this->rules)) {
            throw new ConfigurationException("No rule for {$tokenName} defined.");
        }

        return $this->rules[$tokenName];
    }
}
