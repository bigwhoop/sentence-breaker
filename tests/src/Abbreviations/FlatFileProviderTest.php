<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tests\Abbreviations;

use Bigwhoop\SentenceBreaker\Abbreviations\FlatFileProvider;

class FlatFileProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleFile()
    {
        $provider = new FlatFileProvider(__DIR__.'/../../assets/data', ['abbr1']);
        $this->assertEquals(['Dr', 'Prof'], $provider->getValues());
    }

    public function testMultipleFiles()
    {
        $provider = new FlatFileProvider(__DIR__.'/../../assets/data', ['abbr1', 'abbr2']);
        $this->assertEquals(['Dr', 'Mr.', 'Mrs.', 'Ms.', 'Prof'], $provider->getValues());
    }

    public function testPatternMatching()
    {
        $provider = new FlatFileProvider(__DIR__.'/../../assets/data', ['abbr*']);
        $this->assertEquals(['Dr', 'Mr.', 'Mrs.', 'Ms.', 'Prof'], $provider->getValues());

        $provider = new FlatFileProvider(__DIR__.'/../../assets/data', ['*']);
        $this->assertEquals(['Dr', 'Mr.', 'Mrs.', 'Ms.', 'Prof'], $provider->getValues());
    }
}
