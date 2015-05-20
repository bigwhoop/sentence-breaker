<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Analysis\Rules;

class RulesValidator
{
    /** @var string[] */
    private $errors = [];

    /**
     * @param Rules $rules
     *
     * @return bool
     */
    public function validate(Rules $rules)
    {
        $this->errors = [];

        foreach ($rules->getRules() as $rule) {
            foreach ($rule->getPatterns() as $patternIdx => $pattern) {
                $ruleTokenClass = $rule->getTokenClass();
                $numStartTokens = 0;
                $numTokenMatches = 0;

                $patternName = "Pattern {$ruleTokenClass}#{$patternIdx}";

                foreach ($pattern->getTokens() as $patternToken) {
                    $patternTokenClass = $patternToken->getTokenClass();
                    if ($patternTokenClass === $ruleTokenClass) {
                        $numTokenMatches++;
                        if ($patternToken->isStartToken()) {
                            $numStartTokens++;
                        }
                    } elseif ($patternToken->isStartToken()) {
                        $this->addError("$patternName: Only $ruleTokenClass tokens can have the 'isStartToken' property.");
                    }
                }

                if ($numTokenMatches === 0) {
                    $this->addError("$patternName: No $ruleTokenClass tokens found");
                } elseif ($numTokenMatches > 1) {
                    if ($numStartTokens === 0) {
                        $this->addError("$patternName: Multiple $ruleTokenClass tokens found, but none was set as the 'isStartToken'.");
                    } elseif ($numStartTokens > 1) {
                        $this->addError("$patternName: Multiple $ruleTokenClass tokens with 'isStartToken' property found. Only one is allowed.");
                    }
                }
            }
        }

        return count($this->errors) === 0;
    }

    /**
     * @param string $message
     */
    private function addError($message)
    {
        $this->errors[] = $message;
    }

    /**
     * @return string[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
