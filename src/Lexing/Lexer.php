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

use Bigwhoop\SentenceBreaker\Lexing\States;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;

class Lexer
{
    /** @var resource */
    private $input;
    
    /** @var States\State|null */
    private $state;
    
    /** @var int */
    private $pos = 0;
    
    /** @var int */
    private $itemPos = 0;
    
    /** @var Item[] */
    private $items = [];
    
    /**
     * @param string $input
     */
    public function __construct($input)
    {
        $fh = fopen('php://memory', 'r+');
        fwrite($fh, $input);
        rewind($fh);
        
        $this->input = $fh;
        $this->state = new States\TextState();
    }

    /**
     * @return null|string
     */
    public function next()
    {
        fseek($this->input, $this->pos, SEEK_SET);
        
        $c = fread($this->input, 1);
        
        $this->pos++;
        
        if (feof($this->input)) {
            return null;
        }
        
        return $c;
    }

    /**
     * @return null|string
     */
    public function peek()
    {
        $c = $this->next();
        $this->backup();
        
        return $c;
    }

    /**
     * @return bool
     */
    public function hasMoved()
    {
        return $this->pos > $this->itemPos;
    }
    
    public function backup()
    {
        $this->pos--;
    }

    /**
     * @return Item[]
     */
    public function run()
    {
        while ($this->state instanceof States\State) {
            $stateFn = $this->state;
            $this->state = $stateFn($this);
        }
        
        return $this->items;
    }

    /**
     * @param Token $type
     */
    public function emit(Token $type)
    {
        $startPos = $this->itemPos;
        $endPos   = $this->pos;
        
        if ($endPos > $startPos) {
            fseek($this->input, $startPos, SEEK_SET);
            $value = fread($this->input, $endPos - $startPos);
        } else {
            $value = '';
        }
        
        $item = new Item($type, $startPos, $value);
        
        $this->items[] = $item;
        $this->itemPos = $endPos;
    }
    
    public function error($msg, ...$args)
    {
        exit(vsprintf($msg, $args));
    }
}
