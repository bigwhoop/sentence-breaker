<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tests\Rules;

use Bigwhoop\SentenceBreaker\Rules\Rule;
use Bigwhoop\SentenceBreaker\Rules\RulePattern;
use Bigwhoop\SentenceBreaker\Rules\RulePatternToken;
use Bigwhoop\SentenceBreaker\Rules\Rules;
use Bigwhoop\SentenceBreaker\Rules\IniConfiguration;

class IniConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testValidFile()
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

    /**
     * @expectedException \Bigwhoop\SentenceBreaker\Rules\ConfigurationException
     * @expectedExceptionMessage .INI Configuration must contain 'rules' section.
     */
    public function testMissingRulesSection()
    {
        $ini = <<<INI
T_WORD <T_EOF> = 100
INI;

        $config = new IniConfiguration($ini);
        $config->getRules();
    }

    /**
     * @expectedException \Bigwhoop\SentenceBreaker\Rules\ConfigurationException
     * @expectedExceptionMessage Pattern T_WORD T_EOF: Must contain start token.
     */
    public function testMissingStartToken()
    {
        $ini = <<<INI
[rules]
T_WORD T_EOF = 100
INI;

        $config = new IniConfiguration($ini);
        $config->getRules();
    }

    /**
     * @expectedException \Bigwhoop\SentenceBreaker\Rules\ConfigurationException
     * @expectedExceptionMessage Pattern T_WORD AA <T_EOF>: Token AA must exceed 2 characters.
     */
    public function testInvalidTokenLength()
    {
        $ini = <<<INI
[rules]
T_WORD AA <T_EOF> = 100
INI;

        $config = new IniConfiguration($ini);
        $config->getRules();
    }
}
