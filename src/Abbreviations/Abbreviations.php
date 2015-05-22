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

class Abbreviations
{
    /** @var string[] */
    private $abbreviations = [];

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
    public function addAbbreviations(array $abbreviations)
    {
        foreach ($abbreviations as $abbreviation) {
            $this->addAbbreviation($abbreviation);
        }
    }

    /**
     * @param string $abbreviation
     */
    public function addAbbreviation($abbreviation)
    {
        $abbreviation = $this->normalizeAbbreviation($abbreviation);

        $this->abbreviations[$abbreviation] = $abbreviation;
    }

    /**
     * @param string $abbreviation
     *
     * @return string
     */
    private function normalizeAbbreviation($abbreviation)
    {
        return rtrim($abbreviation, '.');
    }

    /**
     * @return string[]
     */
    public function getAbbreviations()
    {
        return array_keys($this->abbreviations);
    }

    /**
     * @param string $abbreviation
     *
     * @return bool
     */
    public function hasAbbreviation($abbreviation)
    {
        return array_key_exists($this->normalizeAbbreviation($abbreviation), $this->abbreviations);
    }
}
