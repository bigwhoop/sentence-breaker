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
use Bigwhoop\SentenceBreaker\Rules\XMLConfiguration;

class XMLConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testValidFile()
    {
        $config = XMLConfiguration::loadFile(__DIR__.'/../../assets/rules.xml');

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
     * @expectedExceptionMessage Pattern T_EOF/#0 must have unambiguous start token. No T_EOF token found.
     */
    public function testPatternMissingStartToken()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rules>
    <rule>
        <token>T_EOF</token>
        <patterns>
            <pattern probability="50">
                <token>T_SOMETHING</token>
            </pattern>
        </patterns>
    </rule>
</rules>
XML;

        $config = new XMLConfiguration($xml);
        $config->getRules();
    }

    /**
     * @expectedException \Bigwhoop\SentenceBreaker\Rules\ConfigurationException
     * @expectedExceptionMessage Pattern T_EOF/#0 must have unambiguous start token. Multiple T_EOF tokens found.
     */
    public function testUnambiguousPatternBecauseMultiplePotentialStartTokens()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rules>
    <rule>
        <token>T_EOF</token>
        <patterns>
            <pattern probability="50">
                <token>T_EOF</token>
                <token>T_EOF</token>
            </pattern>
        </patterns>
    </rule>
</rules>
XML;

        $config = new XMLConfiguration($xml);
        $config->getRules();
    }

    /**
     * @expectedException \Bigwhoop\SentenceBreaker\Rules\ConfigurationException
     * @expectedExceptionMessage Pattern T_EOF#0: Multiple T_EOF tokens with 'is_start_token' attribute found. Only one is allowed.
     */
    public function testUnambiguousPatternBecauseMultipleStartTokens()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rules>
    <rule>
        <token>T_EOF</token>
        <patterns>
            <pattern probability="50">
                <token is_start_token="true">T_EOF</token>
                <token is_start_token="true">T_EOF</token>
            </pattern>
        </patterns>
    </rule>
</rules>
XML;

        $config = new XMLConfiguration($xml);
        $config->getRules();
    }

    /**
     * @expectedException \Bigwhoop\SentenceBreaker\Rules\ConfigurationException
     * @expectedExceptionMessage Pattern T_EOF#0: Only T_EOF tokens can have the 'is_start_token' attribute.
     */
    public function testPatternWithStartTokensOnWrongToken()
    {
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rules>
    <rule>
        <token>T_EOF</token>
        <patterns>
            <pattern probability="50">
                <token>T_EOF</token>
                <token is_start_token="true">T_SOMETHING</token>
            </pattern>
        </patterns>
    </rule>
</rules>
XML;

        $config = new XMLConfiguration($xml);
        $config->getRules();
    }
}
