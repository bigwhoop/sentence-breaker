<?php
/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Lexing;

use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;

class Item
{
    /** @var Token */
    private $type;
    
    /** @var int */
    private $startPos;
    
    /** @var mixed */
    private $value;

    /**
     * @param Token $type
     * @param int   $startPos
     * @param mixed $value
     */
    public function __construct(Token $type, $startPos, $value = null)
    {
        $this->type     = $type;
        $this->startPos = $startPos;
        $this->value    = $value;
    }

    /**
     * @return Token
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getStartPos()
    {
        return $this->startPos;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
