<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker;

use Bigwhoop\SentenceBreaker\Abbreviations\Abbreviations;
use Bigwhoop\SentenceBreaker\Abbreviations\ArrayProvider;
use Bigwhoop\SentenceBreaker\Abbreviations\ValueProvider;
use Bigwhoop\SentenceBreaker\Exceptions\InvalidArgumentException;
use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Rules\Configuration;
use Bigwhoop\SentenceBreaker\Rules\ConfigurationException;
use Bigwhoop\SentenceBreaker\Rules\IniConfiguration;
use Bigwhoop\SentenceBreaker\Rules\Rules;

class SentenceBreaker
{
    /** @var ValueProvider[] */
    private array $abbreviationProviders = [];

    private Lexer $lexer;

    private ProbabilityCalculator $probabilityCalculator;

    private SentenceBuilder $sentenceBuilder;

    /**
     * @throws ConfigurationException
     */
    public function __construct(?Configuration $rulesConfig = null)
    {
        $rules = $rulesConfig ? $rulesConfig->getRules() : $this->loadDefaultRules();

        $this->setLexer(new Lexer());
        $this->setSentenceBoundaryProbabilityCalculator(new ProbabilityCalculator($rules));
        $this->setSentenceBuilder(new SentenceBuilder());
    }

    /**
     * @throws ConfigurationException
     */
    private function loadDefaultRules(): Rules
    {
        return IniConfiguration::loadFile(__DIR__ . '/../rules/rules.ini')->getRules();
    }

    public function setLexer(Lexer $lexer): void
    {
        $this->lexer = $lexer;
    }

    public function setSentenceBoundaryProbabilityCalculator(ProbabilityCalculator $calculator): void
    {
        $this->probabilityCalculator = $calculator;
    }

    public function setSentenceBuilder(SentenceBuilder $builder): void
    {
        $this->sentenceBuilder = $builder;
    }

    public function setRules(Rules $rules): void
    {
        $this->probabilityCalculator->setRules($rules);
    }

    public function addRules(Rules $rules): void
    {
        $this->probabilityCalculator->addRules($rules);
    }

    /**
     * @param array<string>|ValueProvider $values
     *
     * @throws InvalidArgumentException
     */
    public function addAbbreviations($values): void
    {
        if (is_array($values)) {
            $values = new ArrayProvider($values);
        } elseif (!($values instanceof ValueProvider)) { // @phpstan-ignore-line
            throw new InvalidArgumentException('Values argument must either be an array or an instance of ' . ValueProvider::class);
        }

        $this->abbreviationProviders[] = $values;
    }

    /**
     * @return \Generator<string>
     *
     * @throws ConfigurationException
     * @throws Lexing\States\StateException
     */
    public function split(string $text): \Generator
    {
        $this->probabilityCalculator->setAbbreviations($this->getAbbreviations());

        $tokens = $this->lexer->run($text);

        $probabilities = $this->probabilityCalculator->calculate($tokens);

        return $this->sentenceBuilder->build($probabilities);
    }

    private function getAbbreviations(): Abbreviations
    {
        $abbreviations = new Abbreviations();

        foreach ($this->abbreviationProviders as $provider) {
            $abbreviations->addAbbreviations($provider->getValues());
        }

        return $abbreviations;
    }
}
