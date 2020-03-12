<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Tests\Rules;

use Bigwhoop\SentenceBreaker\Rules\ConfigurationException;
use Bigwhoop\SentenceBreaker\Rules\Rule;
use Bigwhoop\SentenceBreaker\Rules\RulePattern;
use Bigwhoop\SentenceBreaker\Rules\RulePatternToken;
use Bigwhoop\SentenceBreaker\Rules\Rules;
use Bigwhoop\SentenceBreaker\Rules\IniConfiguration;
use PHPUnit\Framework\TestCase;

class IniConfigurationTest extends TestCase
{
    public function testValidFile(): void
    {
        $config = IniConfiguration::loadFile(__DIR__.'/../../assets/rules.ini');

        $this->assertEquals(new Rules([
            new Rule('T_EOF', [
               new RulePattern(100, [
                   new RulePatternToken('T_EOF', true),
               ]),
            ]),
            new Rule('T_PERIOD', [
                new RulePattern(75, [
                    new RulePatternToken('T_PERIOD'),
                    new RulePatternToken('T_PERIOD'),
                    new RulePatternToken('T_PERIOD', true),
                    new RulePatternToken('T_WHITESPACE'),
                    new RulePatternToken('T_CAPITALIZED_WORD'),
                ]),
                new RulePattern(0, [
                    new RulePatternToken('T_ABBREVIATION'),
                    new RulePatternToken('T_PERIOD', true),
                ]),
            ]),
        ]), $config->getRules());
    }

    public function testMissingRulesSection(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('.INI Configuration must contain \'rules\' section.');
        
        $ini = <<<INI
T_WORD <T_EOF> = 100
INI;

        $config = new IniConfiguration($ini);
        $config->getRules();
    }
    
    public function testMissingStartToken(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Pattern T_WORD T_EOF: Must contain start token.');
        
        $ini = <<<INI
[rules]
T_WORD T_EOF = 100
INI;

        $config = new IniConfiguration($ini);
        $config->getRules();
    }

    public function testInvalidTokenLength(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Pattern T_WORD AA <T_EOF>: Token AA must exceed 2 characters.');
        
        $ini = <<<INI
[rules]
T_WORD AA <T_EOF> = 100
INI;

        $config = new IniConfiguration($ini);
        $config->getRules();
    }
}
