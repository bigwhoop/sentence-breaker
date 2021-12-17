<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Abbreviations;

class Abbreviations
{
    /** @var string[] */
    private array $abbreviations = [];

    /**
     * @param string[] $abbreviations
     */
    public function __construct(array $abbreviations = [])
    {
        $this->addAbbreviations($abbreviations);
    }

    /**
     * @param string[] $abbreviations
     */
    public function addAbbreviations(array $abbreviations): void
    {
        foreach ($abbreviations as $abbreviation) {
            $this->addAbbreviation($abbreviation);
        }
    }

    public function addAbbreviation(string $abbreviation): void
    {
        $abbreviation = $this->normalizeAbbreviation($abbreviation);

        $this->abbreviations[$abbreviation] = $abbreviation;
    }

    private function normalizeAbbreviation(string $abbreviation): string
    {
        return rtrim($abbreviation, '.');
    }

    /**
     * @return string[]
     */
    public function getAbbreviations(): array
    {
        return array_keys($this->abbreviations);
    }

    public function hasAbbreviation(string $abbreviation): bool
    {
        return array_key_exists($this->normalizeAbbreviation($abbreviation), $this->abbreviations);
    }
}
