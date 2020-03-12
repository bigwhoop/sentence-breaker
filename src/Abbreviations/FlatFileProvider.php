<?php

declare(strict_types=1);

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
    public function __construct(string $basePath, array $fileNames)
    {
        $this->basePath = $basePath;
        $this->fileNames = $fileNames;
    }

    public function getValues(): array 
    {
        $values = [];
        foreach ($this->getPaths() as $path) {
            $values = array_merge($values, file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        }

        $values = array_unique($values);
        sort($values);

        return $values;
    }

    private function getPaths(): array
    {
        return glob($this->basePath.'/{'.implode(',', $this->fileNames).'}.txt', GLOB_BRACE);
    }
}
