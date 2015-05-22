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

class IniConfiguration implements Configuration
{
    /** @var string */
    private $data;

    /**
     * @param string $path
     *
     * @return IniConfiguration
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
     * @param string $data
     */
    public function __construct($data)
    {
        $this->data = parse_ini_string($data, true);
    }

    /**
     * @inheritdoc
     */
    public function getRules()
    {
        $rules = new Rules();

        if (!array_key_exists('rules', $this->data)) {
            throw new ConfigurationException(".INI Configuration must contain 'rules' section.");
        }

        foreach ($this->data['rules'] as $patternStr => $probability) {
            $pattern = new RulePattern((int) $probability);

            $startToken = '';

            foreach (explode(' ', $patternStr) as $tokenStr) {
                if (strlen($tokenStr) <= 2) {
                    throw new ConfigurationException("Pattern $patternStr: Token $tokenStr must exceed 2 characters.");
                }

                $isStartToken = false;
                if ($tokenStr[0] === '<' && substr($tokenStr, -1) === '>') {
                    $isStartToken = true;
                    $tokenStr = substr($tokenStr, 1, -1);
                    $startToken = $tokenStr;
                }

                $token = new RulePatternToken($tokenStr, $isStartToken);

                $pattern->addToken($token);
            }

            if (empty($startToken)) {
                throw new ConfigurationException("Pattern $patternStr: Must contain start token.");
            }

            $rule = new Rule($startToken);
            $rule->addPattern($pattern);
            $rules->addRule($rule);
        }

        return $rules;
    }
}
