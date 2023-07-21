<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Abbreviations;

use Bigwhoop\SentenceBreaker\Exceptions\Exception;

class FlatFileProvider implements ValueProvider
{
    private string $basePath;

    /** @var string[] */
    private array $fileNames;

    /**
     * @param string[] $fileNames
     */
    public function __construct(string $basePath, array $fileNames)
    {
        $this->basePath = $basePath;
        $this->fileNames = $fileNames;
    }

    /**
     * @return array<string>
     *
     * @throws Exception
     */
    public function getValues(): array
    {
        $values = [];
        foreach ($this->getPaths() as $path) {
            $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if ($lines === false) {
                throw new Exception(sprintf('Unable to read file "%s"', $path));
            }

            $values = array_merge($values, $lines);
        }

        $values = array_unique($values);
        sort($values);

        return $values;
    }

    /**
     * @return array<string>
     */
    private function getPaths(): array
    {
        $pattern = $this->basePath . '/{' . implode(',', $this->fileNames) . '}.txt';
        $paths = glob($pattern, GLOB_BRACE);
        if ($paths === false) {
            throw new Exception(sprintf('Unable to find files with pattern "%s"', $pattern));
        }

        return $paths;
    }
}
