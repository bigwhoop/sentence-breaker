<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\States;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\QuotedStringToken;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WordToken;

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

        $text = '';
        $doDecadeCheck = $leftMark === "'";

        while (true) {
            // match something like "'90s" oder "'80er"
            if ($text !== '' && $doDecadeCheck) {
                if (preg_match('/^\d+/', $text)) {
                    if (preg_match('/^\d+(s|er)$/', $text)) {
                        $lexer->emit(new WordToken($leftMark . $text));

                        return new TextState();
                    }
                } else {
                    // we can stop checking if a quote doesn't start with a numeric string
                    // or if such a numeric string didn't end up being a decade representation. 
                    $doDecadeCheck = false;
                }
            }

            $next = $lexer->next();

            if ($next === null) {
                throw new StateException('Failed to find end of quote. Reached end of input. Read: ' . $lexer->getTokenValue());
            }

            $text .= $next;

            if ($next === $rightMark) {
                break;
            }
        }

        $lexer->emit(new QuotedStringToken());

        return new TextState();
    }
}
