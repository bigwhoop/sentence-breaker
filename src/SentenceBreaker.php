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

use Bigwhoop\SentenceBreaker\BoundaryProbabilityCalculation\Calculator;
use Bigwhoop\SentenceBreaker\BoundaryProbabilityCalculation\TokenProbability;
use Bigwhoop\SentenceBreaker\Configuration\ArrayProvider;
use Bigwhoop\SentenceBreaker\Configuration\ValueProvider;
use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;

class SentenceBreaker
{
    /** @var ValueProvider[] */
    private $abbreviationProviders = [];
    
    /** @var Lexer */
    private $lexer;
    
    /** @var Calculator */
    private $probabilityCalculator;
    
    public function __construct()
    {
        $this->lexer = new Lexer();
        $this->probabilityCalculator = new Calculator();
    }

    /**
     * @param array|ValueProvider $values
     * @throws InvalidArgumentException
     */
    public function addAbbreviations($values)
    {
        if (is_array($values)) {
            $values = new ArrayProvider($values);
        } elseif (!($values instanceof ValueProvider)) {
            throw new InvalidArgumentException("Values argument must either be an array or an instance of " . ValueProvider::class);
        }
        
        $this->abbreviationProviders[] = $values;
    }

    /**
     * @param string $text
     * @return string[]
     */
    public function split($text)
    {
        $this->probabilityCalculator->setAbbreviations($this->getAbbreviations());
        
        $tokens        = $this->lexer->run($text);
        $probabilities = $this->probabilityCalculator->calculate($tokens);
        $sentences     = $this->assembleSentence($probabilities);
        
        return $sentences;
    }

    /**
     * @param TokenProbability[] $tokenProbabilities
     * @param int $threshold
     * @return array
     */
    private function assembleSentence(array $tokenProbabilities, $threshold = 50)
    {
        $sentences = [''];
        
        $numTokens = count($tokenProbabilities);
        
        foreach ($tokenProbabilities as $idx => $tokenProbability) {
            $isLastToken = $idx + 1 === $numTokens;
            
            $token = $tokenProbability->getToken();
            
            $sentenceIdx = count($sentences) - 1;
            $sentences[$sentenceIdx] .= $token instanceof Token ? $token->getPrintableValue() : $token;

            if ($tokenProbability->getProbability() >= $threshold && !$isLastToken && !empty(trim($sentences[$sentenceIdx]))) {
                $sentences[] = '';
            }
        }
        
        $sentences = array_map('ltrim', $sentences);
        
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
        
        asort($values);
        $values = array_unique($values);
        
        return $values;
    }
}
