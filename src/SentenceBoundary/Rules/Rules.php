<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\SentenceBoundary\Rules;

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
        $this->rules[] = $rule;
    }

    /**
     * @return Rule[]
     */
    public function getRules()
    {
        return $this->rules;
    }
}
