<?php
/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\BoundaryProbabilityCalculation;

use Bigwhoop\SentenceBreaker\Lexing\Tokens\ExclamationPointToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\PeriodToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuestionMarkToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuotedStringToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;

class Calculator
{
    /** @var Token[]|string[] */
    private $tokens = [];
    
    /** @var int */
    private $currentIdx = 0;
    
    /** @var string[] */
    private $abbreviations = [];
    
    /**
     * @param Token[]|string[] $tokens
     */
    public function __construct(array $tokens)
    {
        if (count($tokens) < 2) {
            throw new \InvalidArgumentException("Need at least 2 tokens.");
        }
        
        $this->tokens = array_values($tokens);
    }

    /**
     * @param array $abbreviations
     */
    public function setAbbreviations(array $abbreviations)
    {
        $this->abbreviations = $this->normalizeAbbreviations($abbreviations);
    }

    /**
     * @param array $abbreviations
     * @return array
     */
    private function normalizeAbbreviations(array $abbreviations)
    {
        return array_map(function($abbreviation) {
            return rtrim($abbreviation, '.');
        }, $abbreviations);
    }

    /**
     * @param int $threshold
     * @return array
     */
    public function calculate($threshold = 50)
    {
        $sentences = [''];
        
        for ($this->currentIdx = 0, $c = count($this->tokens); $this->currentIdx < $c; $this->currentIdx++) {
            $prop = $this->calculateCurrentTokenProbability();
            
            $currentToken = $this->getToken();
            
            $sentenceIdx = count($sentences) - 1;
            $sentences[$sentenceIdx] .= $currentToken instanceof Token ? $currentToken->getPrintableValue() : ' ' . $currentToken;
            
            if ($prop >= $threshold && $this->currentIdx !== $c - 1) {
                $sentences[] = '';
            }
            
            /*echo sprintf(
                '% 3d%% - %s %s %s' . PHP_EOL,
                $prop,
                $this->getToken(-1) instanceof Token ? $this->getToken(-1)->getName() : '"' . $this->getToken(-1) . '"',
                $this->getToken() instanceof Token ? $this->getToken()->getName() : '"' . $this->getToken() . '"',
                $this->getToken(1) instanceof Token ? $this->getToken(+1)->getName() : '"' . $this->getToken(+1) . '"'
            );*/
        }
        
        $sentences = array_map('ltrim', $sentences);
        
        return $sentences;
    }

    /**
     * @return int
     */
    private function calculateCurrentTokenProbability()
    {
        $prop = 0;
        
        $currentToken = $this->getToken();
        if ($currentToken instanceof QuestionMarkToken || $currentToken instanceof ExclamationPointToken) {
            $prop = 100;
        } elseif ($currentToken instanceof PeriodToken) {
            $prevToken = $this->getToken(-1);
            if (is_string($prevToken)) {
                if (false !== strpos($prevToken, '.')) {
                    $nextToken = $this->getToken(+1);
                    if (is_string($nextToken) && ctype_upper(substr($nextToken, 0, 1))) {
                        $prop = 60;
                    } else {
                        $prop = 25;
                    }
                } elseif (in_array($prevToken, $this->abbreviations)) {
                    $prop = 0;
                } else {
                    $prop = 75;
                }
            } else {
                $prop = 50;
            }
        } elseif ($currentToken instanceof QuotedStringToken) {
            $nextToken = $this->getToken(+1);
            if (is_string($nextToken) && ctype_upper(substr($nextToken, 0, 1))) {
                $prop = 80;
            } else {
                $prop = 40;
            }
        }
        
        return $prop;
    }

    /**
     * @param int $offset
     * @return Token|null|string
     */
    private function getToken($offset = 0)
    {
        $idx = $this->currentIdx + $offset;
        
        if (!array_key_exists($idx, $this->tokens)) {
            return null;
        }
        
        return $this->tokens[$idx];
    }
}
