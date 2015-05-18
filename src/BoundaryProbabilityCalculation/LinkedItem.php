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

class LinkedItem
{
    /** @var Item */
    private $item;
    
    /** @var LinkedItem|null */
    private $prev;
    
    /** @var LinkedItem|null */
    private $next;

    /**
     * @param Item $item
     * @param LinkedItem $prev
     * @param LinkedItem $next
     */
    public function __construct(Item $item, LinkedItem $prev = null, LinkedItem $next = null)
    {
        $this->item = $item;
        $this->prev = $prev;
        $this->next = $next;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return bool
     */
    public function hasPrev()
    {
        return !!$this->prev;
    }

    /**
     * @param LinkedItem|null $item
     */
    public function setPrev(LinkedItem $item = null)
    {
        $this->prev = $item;
    }

    /**
     * @return LinkedItem|null
     */
    public function getPrev()
    {
        return $this->prev;
    }

    /**
     * @return bool
     */
    public function hasNext()
    {
        return !!$this->next;
    }

    /**
     * @param LinkedItem|null $item
     */
    public function setNext(LinkedItem $item = null)
    {
        $this->next = $item;
    }

    /**
     * @return LinkedItem|null
     */
    public function getNext()
    {
        return $this->next;
    }
}
