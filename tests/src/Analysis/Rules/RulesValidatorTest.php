<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tests\Analysis\Rules;

use Bigwhoop\SentenceBreaker\Analysis\Rules\RulesValidator;
use Bigwhoop\SentenceBreaker\Analysis\Rules\XMLConfiguration;

class RulesValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testPatternMissingToken()
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
        $rules = $config->getRules();
        $validator = new RulesValidator();

        $this->assertFalse($validator->validate($rules));
        $this->assertEquals([
            'Pattern T_EOF#0: No T_EOF tokens found',
        ], $validator->getErrors());
    }

    public function testPatternMissingStartToken()
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
        $rules = $config->getRules();
        $validator = new RulesValidator();

        $this->assertFalse($validator->validate($rules));
        $this->assertEquals([
            'Pattern T_EOF#0: Multiple T_EOF tokens found, but none was set as the \'isStartToken\'.',
        ], $validator->getErrors());
    }

    public function testPatternWithMultipleStartTokens()
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
        $rules = $config->getRules();
        $validator = new RulesValidator();

        $this->assertFalse($validator->validate($rules));
        $this->assertEquals([
            'Pattern T_EOF#0: Multiple T_EOF tokens with \'isStartToken\' property found. Only one is allowed.',
        ], $validator->getErrors());
    }

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
        $rules = $config->getRules();
        $validator = new RulesValidator();

        $this->assertFalse($validator->validate($rules));
        $this->assertEquals([
            'Pattern T_EOF#0: Only T_EOF tokens can have the \'isStartToken\' property.',
        ], $validator->getErrors());
    }
}
