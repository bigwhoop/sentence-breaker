<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing;

use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\ValueToken;

class Lexer
{
    /** @var string[] */
    private array $inputChars = [];

    private ?States\State $state;

    /** @var int The current position in */
    private int $pos = 0;

    /** @var int The start position of the current token */
    private int $tokenPos = 0;

    /** @var Token[] */
    private array $tokens = [];

    public function __construct()
    {
        $this->setInput('');
    }

    /**
     * @return iterable<Token>
     *
     * @throws States\StateException
     */
    public function run(string $input): iterable
    {
        $this->setInput($input);

        while ($this->state instanceof States\State) {
            $stateFn = $this->state;
            $this->state = $stateFn($this);
            yield from $this->tokens;
            $this->tokens = [];
        }

        yield from $this->tokens;
    }

    private function setInput(string $input): void
    {
        $this->reset();
        $this->inputChars = mb_str_split($input);
    }

    private function reset(): void
    {
        $this->pos = 0;
        $this->tokenPos = 0;
        $this->tokens = [];
        $this->state = new States\TextState();
        $this->inputChars = [];
    }

    public function pos(): int
    {
        return $this->pos;
    }

    public function next(int $offset = 0): ?string
    {
        $next = $this->inputChars[$this->pos + $offset] ?? null;
        $this->pos += $offset + 1;

        return $next;
    }

    public function last(): ?string
    {
        if ($this->pos === 0) {
            return null;
        }

        return $this->inputChars[$this->pos - 1] ?? null;
    }

    public function peek(int $offset = 0): ?string
    {
        return $this->inputChars[$this->pos + $offset] ?? null;
    }

    public function backup(int $offset = 0): void
    {
        $this->pos -= 1 + $offset;
    }

    public function ignore(): void
    {
        $this->tokenPos = $this->pos;
    }

    public function getTokenValue(): string
    {
        $startPos = $this->tokenPos;
        $endPos = $this->pos;

        if ($endPos <= $startPos) {
            return '';
        }

        return implode('', array_slice($this->inputChars, $startPos, $endPos - $startPos));
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
