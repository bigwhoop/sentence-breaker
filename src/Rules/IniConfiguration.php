<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Rules;

class IniConfiguration implements Configuration
{
    /**
     * @var array<mixed>
     */
    private array $data = [];

    /**
     * @throws ConfigurationException
     */
    public static function loadFile(string $path): self
    {
        if (!is_readable($path)) {
            throw new ConfigurationException("File '{$path}' must be readable.");
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new ConfigurationException("Unable to read '{$path}'.");
        }

        return new self($contents);
    }

    /**
      @throws ConfigurationException
     */
    public function __construct(string $data)
    {
        $parsedData = parse_ini_string($data, true);
        if ($parsedData === false) {
            throw new ConfigurationException('Failed parsing given INI string');
        }

        $this->data = $parsedData;
    }

    public function getRules(): Rules
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
