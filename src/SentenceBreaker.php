<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker;

use Bigwhoop\SentenceBreaker\Abbreviations\Abbreviations;
use Bigwhoop\SentenceBreaker\Exceptions\InvalidArgumentException;
use Bigwhoop\SentenceBreaker\Rules\Configuration;
use Bigwhoop\SentenceBreaker\Rules\IniConfiguration;
use Bigwhoop\SentenceBreaker\Abbreviations\ArrayProvider;
use Bigwhoop\SentenceBreaker\Abbreviations\ValueProvider;
use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Rules\Rules;

class SentenceBreaker
{
    /** @var ValueProvider[] */
    private $abbreviationProviders = [];

    /** @var Lexer */
    private $lexer;

    /** @var ProbabilityCalculator */
    private $probabilityCalculator;

    /** @var SentenceBuilder */
    private $sentenceBuilder;

    /**
     * @param Configuration|null $rulesConfig
     */
    public function __construct(Configuration $rulesConfig = null)
    {
        $rules = $rulesConfig ? $rulesConfig->getRules() : $this->loadDefaultRules();

        $this->setLexer(new Lexer());
        $this->setSentenceBoundaryProbabilityCalculator(new ProbabilityCalculator($rules));
        $this->setSentenceBuilder(new SentenceBuilder());
    }

    /**
     * @return Rules
     */
    private function loadDefaultRules()
    {
        return IniConfiguration::loadFile(__DIR__.'/../rules/rules.ini')->getRules();
    }

    /**
     * @param Lexer $lexer
     */
    public function setLexer(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * @param ProbabilityCalculator $calculator
     */
    public function setSentenceBoundaryProbabilityCalculator(ProbabilityCalculator $calculator)
    {
        $this->probabilityCalculator = $calculator;
    }

    /**
     * @param SentenceBuilder $builder
     */
    public function setSentenceBuilder(SentenceBuilder $builder)
    {
        $this->sentenceBuilder = $builder;
    }

    /**
     * @param Rules $rules
     */
    public function setRules(Rules $rules)
    {
        $this->probabilityCalculator->setRules($rules);
    }

    /**
     * @param Rules $rules
     */
    public function addRules(Rules $rules)
    {
        $this->probabilityCalculator->addRules($rules);
    }

    /**
     * @param array|ValueProvider $values
     *
     * @throws InvalidArgumentException
     */
    public function addAbbreviations($values)
    {
        if (is_array($values)) {
            $values = new ArrayProvider($values);
        } elseif (!($values instanceof ValueProvider)) {
            throw new InvalidArgumentException('Values argument must either be an array or an instance of '.ValueProvider::class);
        }

        $this->abbreviationProviders[] = $values;
    }

    /**
     * @param string $text
     *
     * @return string[]
     */
    public function split($text)
    {
        $this->probabilityCalculator->setAbbreviations($this->getAbbreviations());

        $tokens = $this->lexer->run($text);
        $probabilities = $this->probabilityCalculator->calculate($tokens);
        $sentences = $this->sentenceBuilder->build($probabilities);

        return $sentences;
    }

    /**
     * @return Abbreviations
     */
    private function getAbbreviations()
    {
        $abbreviations = new Abbreviations();

        foreach ($this->abbreviationProviders as $provider) {
            $abbreviations->addAbbreviations($provider->getValues());
        }

        return $abbreviations;
    }
}
