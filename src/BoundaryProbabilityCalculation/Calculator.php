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

use Bigwhoop\SentenceBreaker\Lexing\Item;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\EOFToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\ExclamationPointToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\PeriodToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuestionMarkToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\SpaceToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WordToken;

class Calculator
{
    /**
     * @param Item[] $items
     */
    public function calculate(array $items)
    {
        if (count($items) < 2) {
            throw new \InvalidArgumentException("Need at least 2 items.");
        }
        
        /** @var LinkedItem[] $linkedItems */
        $linkedItems = [];
        foreach ($items as $item) {
            $linkedItems[] = new LinkedItem($item);
        }
        
        for ($i = 0, $c = count($linkedItems); $i < $c; $i++) {
            $item = $linkedItems[$i];
            $item->setPrev($i == 0 ? null : $linkedItems[$i - 1]);
            $item->setNext($i + 1 < $c ? $linkedItems[$i + 1] : null);
        }
         
        foreach ($linkedItems as $linkedItem) {
            $prop = $this->calcProbability($linkedItem);
            
            echo $linkedItem->getItem()->toString() . ' = ' . $prop . PHP_EOL; 
        }
        exit();
    }

    /**
     * @param LinkedItem $linkedItem
     * @return int
     */
    private function calcProbability(LinkedItem $linkedItem)
    {
        $prop = 0;
        
        if (!$linkedItem->hasPrev()) {
            return $prop;
        }
        
        $currentType = $linkedItem->getItem()->getType();
        
        // ! or . or ?
        if ($currentType instanceof PeriodToken || $currentType instanceof QuestionMarkToken || $currentType instanceof ExclamationPointToken) {
            // ... as the last item
            if (!$linkedItem->hasNext() || $linkedItem->getNext()->getItem()->getType() instanceof EOFToken) {
                $prop += 100;
            }
            
            // ... followed by SPACE
            if ($linkedItem->hasNext() && $linkedItem->getNext()->getItem()->getType() instanceof SpaceToken) {
                $prop += 25;
                
                // ... followed by uppercase word
                if ($linkedItem->getNext()->hasNext()) {
                    $nextNext = $linkedItem->getNext()->getNext()->getItem();
                    $nextNextValue = $nextNext->getValue();
                    
                    if ($nextNext->getType() instanceof WordToken && !empty($nextNextValue) && ctype_upper(substr($nextNextValue, 0, 1))) {
                        $prop += 50;
                    }
                }
            }
        }
        
        return $prop;
    }
}
