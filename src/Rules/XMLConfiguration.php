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

class XMLConfiguration implements Configuration
{
    /** @var \SimpleXMLElement */
    private $data;

    /**
     * @param string $path
     *
     * @return XMLConfiguration
     *
     * @throws ConfigurationException
     */
    public static function loadFile($path)
    {
        if (!is_readable($path)) {
            throw new ConfigurationException("File '{$path}' must be readable.");
        }

        return new self(file_get_contents($path));
    }

    /**
     * @param string $xml
     */
    public function __construct($xml)
    {
        $this->load($xml);
    }

    /**
     * @param string $xml
     *
     * @throws ConfigurationException
     */
    private function load($xml)
    {
        $this->data = simplexml_load_string($xml);
    }

    /**
     * @inheritdoc
     */
    public function getRules()
    {
        $data = $this->data;

        $rules = new Rules();

        foreach ($data->rule as $ruleData) {
            $rule = new Rule((string) $ruleData->token);

            $patternIdx = 0;
            foreach ($ruleData->patterns->pattern as $patternData) {
                $patternName = "Pattern {$rule->getTokenName()}#{$patternIdx}";

                $hasStartToken = false;
                $potentialStartTokenIdxs = [];
                $tokens = [];

                foreach ($patternData->token as $tokenData) {
                    $tokenName = (string) $tokenData;
                    $isStartToken = (bool) $tokenData['is_start_token'];

                    $tokens[] = ['name' => $tokenName, 'is_start_token' => $isStartToken];

                    if ($isStartToken) {
                        if ($tokenName !== $rule->getTokenName()) {
                            throw new ConfigurationException("$patternName: Only {$rule->getTokenName()} tokens can have the 'is_start_token' attribute.");
                        }

                        if ($hasStartToken) {
                            throw new ConfigurationException("$patternName: Multiple {$rule->getTokenName()} tokens with 'is_start_token' attribute found. Only one is allowed.");
                        } else {
                            $hasStartToken = true;
                        }
                    }

                    if ($tokenName === $rule->getTokenName()) {
                        $potentialStartTokenIdxs[] = count($tokens) - 1;
                    }
                }

                if (!$hasStartToken) {
                    if (count($potentialStartTokenIdxs) === 0) {
                        throw new ConfigurationException("Pattern {$rule->getTokenName()}/#{$patternIdx} must have unambiguous start token. No {$rule->getTokenName()} token found.");
                    } elseif (count($potentialStartTokenIdxs) > 1) {
                        throw new ConfigurationException("Pattern {$rule->getTokenName()}/#{$patternIdx} must have unambiguous start token. Multiple {$rule->getTokenName()} tokens found.");
                    } else {
                        $potentialStartTokenIdx = $potentialStartTokenIdxs[0];
                        $tokens[$potentialStartTokenIdx]['is_start_token'] = true;
                    }
                }

                $pattern = new RulePattern((int) $patternData['probability']);

                foreach ($tokens as $token) {
                    $pattern->addToken(new RulePatternToken($token['name'], $token['is_start_token']));
                }

                $rule->addPattern($pattern);

                $patternIdx++;
            }

            $rules->addRule($rule);
        }

        return $rules;
    }
}
