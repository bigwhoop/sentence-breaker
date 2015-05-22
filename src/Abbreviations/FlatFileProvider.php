<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Abbreviations;

class FlatFileProvider implements ValueProvider
{
    /** @var string */
    private $basePath;

    /** @var string[] */
    private $fileNames = [];

    /**
     * @param string   $basePath
     * @param string[] $fileNames
     */
    public function __construct($basePath, array $fileNames)
    {
        $this->basePath = $basePath;
        $this->fileNames = $fileNames;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        $values = [];
        foreach ($this->getPaths() as $path) {
            $values = array_merge($values, file($path,  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        }

        $values = array_unique($values);
        sort($values);

        return $values;
    }

    /**
     * @return array
     */
    private function getPaths()
    {
        return glob($this->basePath.'/{'.implode(',', $this->fileNames).'}.txt', GLOB_BRACE);
    }
}
