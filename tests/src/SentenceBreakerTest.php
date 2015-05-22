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

use Bigwhoop\SentenceBreaker\Abbreviations\FlatFileProvider;
use Bigwhoop\SentenceBreaker\SentenceBreaker;

class SentenceBreakerTest extends \PHPUnit_Framework_TestCase
{
    public function testSplitting()
    {
        $breaker = new SentenceBreaker();
        $breaker->addAbbreviations(['Dr', 'Prof']);

        $sentences = $breaker->split("Hello Dr. Jones! How are you? I'm fine, thanks!");

        $this->assertSame(['Hello Dr. Jones!', 'How are you?', "I'm fine, thanks!"], $sentences);
    }

    public function testSplittingWithFlatFileProvider()
    {
        $breaker = new SentenceBreaker();
        $breaker->addAbbreviations(new FlatFileProvider(__DIR__.'/../assets/data', ['*']));

        $sentences = $breaker->split("Hello Dr. Jones! How are you? I'm fine, thanks!");

        $this->assertSame(['Hello Dr. Jones!', 'How are you?', "I'm fine, thanks!"], $sentences);
    }
}
