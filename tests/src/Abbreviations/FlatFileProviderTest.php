<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Tests\Abbreviations;

use Bigwhoop\SentenceBreaker\Abbreviations\FlatFileProvider;
use PHPUnit\Framework\TestCase;

class FlatFileProviderTest extends TestCase
{
    public function testSingleFile(): void
    {
        $provider = new FlatFileProvider(__DIR__.'/../../assets/data', ['abbr1']);
        $this->assertEquals(['Dr', 'Prof'], $provider->getValues());
    }

    public function testMultipleFiles(): void
    {
        $provider = new FlatFileProvider(__DIR__.'/../../assets/data', ['abbr1', 'abbr2']);
        $this->assertEquals(['Dr', 'Mr.', 'Mrs.', 'Ms.', 'Prof'], $provider->getValues());
    }

    public function testPatternMatching(): void
    {
        $provider = new FlatFileProvider(__DIR__.'/../../assets/data', ['abbr*']);
        $this->assertEquals(['Dr', 'Mr.', 'Mrs.', 'Ms.', 'Prof'], $provider->getValues());

        $provider = new FlatFileProvider(__DIR__.'/../../assets/data', ['*']);
        $this->assertEquals(['Dr', 'Mr.', 'Mrs.', 'Ms.', 'Prof'], $provider->getValues());
    }
}
