<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\CapitalizedWordToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WordToken;

class WordState extends State
{
    /**
     * @return array<string|null>
     */
    private function getNonWordChars(): array
    {
        return array_merge(['.', '?', '!', null], WhitespaceState::CHARS);
    }

    /**
     * {@inheritdoc}
     */
    protected function call(Lexer $lexer): ?State
    {
        $nonWordChars = $this->getNonWordChars();

        while (!in_array($lexer->peek(), $nonWordChars, true)) {
            $lexer->next();
        }

        $value = $lexer->getTokenValue();
        $firstChar = $value[0] ?? '';

        if (ctype_upper($firstChar)) {
            $lexer->emit(new CapitalizedWordToken());
        } else {
            $lexer->emit(new WordToken());
        }

        return new TextState();
    }
}
