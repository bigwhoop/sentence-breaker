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

class Rules
{
    /** @var Rule[] */
    private $rules = [];

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
    public function addRules(array $rules)
    {
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * @param Rule $rule
     */
    public function addRule(Rule $rule)
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
    public function getRules()
    {
        return array_values($this->rules);
    }

    /**
     * @param string $tokenName
     *
     * @return bool
     */
    public function hasRule($tokenName)
    {
        return array_key_exists($tokenName, $this->rules);
    }

    /**
     * @param string $tokenName
     *
     * @return Rule
     *
     * @throws ConfigurationException
     */
    public function getRule($tokenName)
    {
        if (!array_key_exists($tokenName, $this->rules)) {
            throw new ConfigurationException("No rule for {$tokenName} defined.");
        }

        return $this->rules[$tokenName];
    }
}
