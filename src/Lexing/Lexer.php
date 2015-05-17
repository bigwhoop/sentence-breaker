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
     * @return string
     */
    public function next()
    {
        fseek($this->input, $this->pos, SEEK_SET);
        
        $c = fread($this->input, 1);
        if (feof($this->input)) {
            exit('EOF');
        }
        
        $this->pos++;
        
        return $c;
    }
    
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
    
    public function run()
    {
        while ($this->state instanceof States\State) {
            $stateFn = $this->state;
            $this->state = $stateFn($this);
        }
    }

    /**
     * @param Token $type
     */
    public function emit(Token $type)
    {
        $startPos = $this->itemPos;
        $endPos   = $this->pos;
        
        fseek($this->input, $startPos, SEEK_SET);
        $value = fread($this->input, $endPos - $startPos);
        
        $this->items[] = new Item($type, $startPos, $value);
        var_dump($this->items[count($this->items) - 1]);
        $this->itemPos = $endPos;
    }
    
    public function error($msg, ...$args)
    {
        exit(vsprintf($msg, $args));
    }
}
