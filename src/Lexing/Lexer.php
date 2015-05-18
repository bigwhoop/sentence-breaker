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
    
    /** @var int The current position in $input */
    private $pos = 0;
    
    /** @var int The start position of the current token */
    private $tokenPos = 0;
    
    /** @var Token[]|string[] */
    private $tokens = [];
    
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
     * @return Token|string|null
     */
    public function getLastToken()
    {
        if (!empty($this->tokens)) {
            return null;
        }
        
        return $this->tokens[count($this->tokens) - 1];
    }

    /**
     * @param int $offset
     * @return null|string
     */
    public function next($offset = 0)
    {
        fseek($this->input, $this->pos + $offset, SEEK_SET);
        
        $c = fread($this->input, 1);
        
        $this->pos++;
        
        if (feof($this->input)) {
            return null;
        }
        
        return $c;
    }

    /**
     * @param int $offset
     * @return null|string
     */
    public function peek($offset = 0)
    {
        $c = $this->next($offset);
        $this->backup();
        
        return $c;
    }

    /**
     * @return bool
     */
    public function hasMoved()
    {
        return $this->pos > $this->tokenPos;
    }
    
    public function backup()
    {
        $this->pos--;
    }

    /**
     * @return Token[]
     */
    public function run()
    {
        while ($this->state instanceof States\State) {
            $stateFn = $this->state;
            $this->state = $stateFn($this);
        }
        
        return $this->tokens;
    }
    
    public function ignore()
    {
        $this->tokenPos = $this->pos;
    }

    /**
     * @return null|string
     */
    public function getTokenValue()
    {
        $startPos = $this->tokenPos;
        $endPos = $this->pos;
        
        $value = null;
        if ($endPos > $startPos) {
            fseek($this->input, $startPos, SEEK_SET);
            $value = fread($this->input, $endPos - $startPos);
        }
        
        return $value;
    }

    /**
     * @param Token|null $token
     */
    public function emit(Token $token = null)
    {
        $value = $this->getTokenValue();
        
        if ($token) {
            $this->tokens[] = $token;
        } elseif ($value !== null) {
            $this->tokens[] = $value;
        }
        
        $this->tokenPos = $this->pos;
    }

    /**
     * @param string $msg
     * @param mixed[] $args
     */
    public function error($msg, ...$args)
    {
        exit(vsprintf($msg, $args));
    }
}
