<?php
/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tests;

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenizer()
    {
        $text = "hello. my name is max! I like it here; in this test case.";

        $expected = [
            "hello. my name is max! I like it here; in this test case."
        ]:
    }
}
