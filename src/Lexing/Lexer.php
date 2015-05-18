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

    public function __construct()
    {
        $this->setInput('');
    }

    /**
     * @param string $input
     *
     * @return Token[]
     */
    public function run($input)
    {
        $this->setInput($input);

        while ($this->state instanceof States\State) {
            $stateFn = $this->state;
            $this->state = $stateFn($this);
        }

        return $this->tokens;
    }

    /**
     * @param string $input
     */
    private function setInput($input)
    {
        $this->reset();

        $fh = fopen('php://memory', 'r+');
        fwrite($fh, $input);
        rewind($fh);

        $this->input = $fh;
    }

    private function reset()
    {
        $this->pos = 0;
        $this->tokenPos = 0;
        $this->tokens = [];
        $this->state = new States\TextState();
    }

    /**
     * @return Token|string|null
     */
    public function getLastToken()
    {
        if (!empty($this->tokens)) {
            return;
        }

        return $this->tokens[count($this->tokens) - 1];
    }

    /**
     * @param int $offset
     *
     * @return null|string
     */
    public function next($offset = 0)
    {
        fseek($this->input, $this->pos + $offset, SEEK_SET);
        $c = fread($this->input, 1);
        $this->pos++;

        if (feof($this->input)) {
            return;
        }

        return $c;
    }

    /**
     * @return null|string
     */
    public function last()
    {
        if ($this->pos === 0) {
            return;
        }

        fseek($this->input, $this->pos - 1, SEEK_SET);
        $c = fread($this->input, 1);

        return $c;
    }

    /**
     * @param int $offset
     *
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
}
