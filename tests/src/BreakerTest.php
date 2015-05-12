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

use Bigwhoop\SentenceBreaker\Breaker;

class BreakerTest extends \PHPUnit_Framework_TestCase
{
    public function testSentencesBreaking()
    {
        $text = <<<TXT
This is sentence one. Sentence two! Sentence three? Sentence "four". Sentence "five"! Sentence "six"? Sentence
"seven." Sentence 'eight!' Dr. Jones said: "Mrs. Smith you have a lovely daughter!" The T.V.A. is a big project!
TXT;
        
        $breaker = new Breaker();
        $actual = $breaker->getSentences($text);
        
        $expected = [
            "This is sentence one.",
            "Sentence two!",
            "Sentence three?",
            'Sentence "four".',
            'Sentence "five"!',
            'Sentence "six"?',
            'Sentence "seven."',
            "Sentence 'eight!'",
            'Dr. Jones said: "Mrs. Smith you have a lovely daughter!"',
            'The T.V.A. is a big project!'
        ];
        
        $this->assertEquals($expected, $actual);
    }
}
