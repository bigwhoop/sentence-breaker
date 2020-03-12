<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Rules;

class XMLConfiguration implements Configuration
{
    /** @var \SimpleXMLElement */
    private $data;

    /**
     * @param string $path
     * @return static
     * @throws ConfigurationException
     */
    public static function loadFile(string $path): self
    {
        if (!is_readable($path)) {
            throw new ConfigurationException("File '{$path}' must be readable.");
        }

        return new self(file_get_contents($path));
    }

    public function __construct(string $xml)
    {
        $this->load($xml);
    }

    private function load(string $xml): void
    {
        $this->data = simplexml_load_string($xml);
    }

    public function getRules(): Rules
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
                        }

                        $hasStartToken = true;
                    }

                    if ($tokenName === $rule->getTokenName()) {
                        $potentialStartTokenIdxs[] = count($tokens) - 1;
                    }
                }

                if (!$hasStartToken) {
                    if (count($potentialStartTokenIdxs) === 0) {
                        throw new ConfigurationException("Pattern {$rule->getTokenName()}/#{$patternIdx} must have unambiguous start token. No {$rule->getTokenName()} token found.");
                    }
                    
                    if (count($potentialStartTokenIdxs) > 1) {
                        throw new ConfigurationException("Pattern {$rule->getTokenName()}/#{$patternIdx} must have unambiguous start token. Multiple {$rule->getTokenName()} tokens found.");
                    } 
                    
                    $potentialStartTokenIdx = $potentialStartTokenIdxs[0];
                    $tokens[$potentialStartTokenIdx]['is_start_token'] = true;
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
