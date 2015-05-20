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

use Bigwhoop\SentenceBreaker\Analysis\Rules\Rule;
use Bigwhoop\SentenceBreaker\Analysis\Rules\RulePattern;
use Bigwhoop\SentenceBreaker\Analysis\Rules\RulePatternToken;
use Bigwhoop\SentenceBreaker\Analysis\Rules\Rules;
use Bigwhoop\SentenceBreaker\Analysis\Rules\XMLConfiguration;

class XMLConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testValidFile()
    {
        $config = XMLConfiguration::loadFile(__DIR__.'/../../../assets/rules.xml');

        $this->assertEquals(new Rules([
            new Rule('T_EOF', [
               new RulePattern(100, [
                   new RulePatternToken('T_EOF'),
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
                    new RulePatternToken('T_PERIOD'),
                ]),
            ]),
        ]), $config->getRules());
    }
}
