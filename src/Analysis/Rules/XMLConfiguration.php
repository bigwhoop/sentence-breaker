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

            foreach ($ruleData->patterns->pattern as $patternData) {
                $pattern = new RulePattern((int) $patternData['probability']);

                foreach ($patternData->token as $tokenData) {
                    $token = new RulePatternToken(
                        (string) $tokenData,
                        (bool) $tokenData['is_start_token']
                    );

                    $pattern->addToken($token);
                }

                $rule->addPattern($pattern);
            }

            $rules->addRule($rule);
        }

        return $rules;
    }
}
