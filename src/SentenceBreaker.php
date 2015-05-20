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

use Bigwhoop\SentenceBreaker\SentenceBoundary\Rules\Configuration;
use Bigwhoop\SentenceBreaker\SentenceBoundary\Rules\Rules;
use Bigwhoop\SentenceBreaker\SentenceBoundary\Rules\RulesValidator;
use Bigwhoop\SentenceBreaker\SentenceBoundary\Rules\XMLConfiguration;
use Bigwhoop\SentenceBreaker\SentenceBoundary\ProbabilityCalculator;
use Bigwhoop\SentenceBreaker\Configuration\ArrayProvider;
use Bigwhoop\SentenceBreaker\Configuration\ValueProvider;
use Bigwhoop\SentenceBreaker\Lexing\Lexer;

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
        $this->validateRules($rules);

        $this->setLexer(new Lexer());
        $this->setSentenceBoundaryProbabilityCalculator(new ProbabilityCalculator($rules));
        $this->setSentenceBuilder(new SentenceBuilder());
    }

    /**
     * @return XMLConfiguration
     *
     * @throws SentenceBoundary\Rules\ConfigurationException
     */
    private function loadDefaultRules()
    {
        return XMLConfiguration::loadFile(__DIR__.'/../rules/rules.xml')->getRules();
    }

    /**
     * @param Rules $rules
     *
     * @throws Exception
     */
    private function validateRules(Rules $rules)
    {
        $validator = new RulesValidator();
        if (!$validator->validate($rules)) {
            throw new Exception('Rules validation failed: '.implode(' | ', $validator->getErrors()));
        }
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
     * @return array
     */
    private function getAbbreviations()
    {
        $values = [];

        foreach ($this->abbreviationProviders as $provider) {
            $values = array_merge($values, $provider->getValues());
        }

        $values = array_unique($values);
        sort($values);

        return $values;
    }
}
