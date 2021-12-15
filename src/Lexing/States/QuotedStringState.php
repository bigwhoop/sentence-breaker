<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuotedStringToken;

class QuotedStringState extends State
{
    private const MARKS = [
        '"' => '"',
        "'" => "'",
        '‘' => '’',
        '“' => '”',
        '„' => '”',
    ];

    /**
     * @return array<string>
     */
    public static function getLeftMarks(): array
    {
        return array_keys(self::MARKS);
    }

    protected function call(Lexer $lexer): ?State
    {
        $leftMark = $lexer->next();
        $rightMark = self::MARKS[$leftMark];

        while (true) {
            $next = $lexer->next();

            if ($next === null) {
                throw new StateException('Failed to find end of quote. Reached end of input. Read: '.$lexer->getTokenValue());
            }

            if ($next === $rightMark) {
                break;
            }
        }

        $lexer->emit(new QuotedStringToken());

        return new TextState();
    }
}
