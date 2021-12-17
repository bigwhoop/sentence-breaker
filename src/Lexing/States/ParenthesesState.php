<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\StringInParenthesesToken;

class ParenthesesState extends State
{
    private const CHARS = [
        '(' => ')',
    ];

    /**
     * @return array<string>
     */
    public static function getOpeningParentheses(): array
    {
        return array_keys(self::CHARS);
    }

    protected function call(Lexer $lexer): ?State
    {
        $opening = $lexer->next();
        $closing = self::CHARS[$opening];

        while (true) {
            $next = $lexer->next();

            if ($next === null) {
                throw new StateException('Failed to find closing parentheses. Reached end of input. Read: '.$lexer->getTokenValue());
            }

            if ($next === $closing) {
                break;
            }
        }

        $lexer->emit(new StringInParenthesesToken());

        return new TextState();
    }
}
