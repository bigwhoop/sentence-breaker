<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing;

use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\ValueToken;

class Lexer
{
    /** @var string */
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
     * @return Token[]
     * @throws States\StateException
     */
    public function run(string $input): array
    {
        $this->setInput($input);

        while ($this->state instanceof States\State) {
            $stateFn = $this->state;
            $this->state = $stateFn($this);
        }

        return $this->tokens;
    }

    private function setInput(string $input): void
    {
        $this->reset();
        $this->input = $input;
    }

    private function reset(): void
    {
        $this->pos = 0;
        $this->tokenPos = 0;
        $this->tokens = [];
        $this->state = new States\TextState();
    }

    public function pos(): int
    {
        return $this->pos;
    }

    public function next(int $offset = 0): ?string
    {
        $c = mb_substr($this->input, $this->pos + $offset, 1);
        $this->pos += 1 + $offset;

        if ($c === '') {
            return null;
        }

        return $c;
    }

    public function last(): ?string
    {
        if ($this->pos === 0) {
            return null;
        }
        
        $c = mb_substr($this->input, $this->pos - 1, 1);

        if ($c === '') {
            return null;
        }

        return $c;
    }

    public function peek(int $offset = 0): ?string
    {
        $c = $this->next($offset);
        $this->backup($offset);

        return $c;
    }

    public function backup(int $offset = 0): void
    {
        $this->pos -= 1 + $offset;
    }

    public function ignore(): void
    {
        $this->tokenPos = $this->pos;
    }

    public function getTokenValue(): ?string
    {
        $startPos = $this->tokenPos;
        $endPos = $this->pos;

        $value = null;
        if ($endPos > $startPos) {
            $value = mb_substr($this->input, $startPos, $endPos - $startPos);
        }

        return $value;
    }

    public function emit(Token $token): void
    {
        if ($token instanceof ValueToken) {
            $token->setValue($this->getTokenValue());
        }

        $this->tokens[] = $token;

        $this->tokenPos = $this->pos;
    }
}
