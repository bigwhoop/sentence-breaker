<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Tests\Lexing;

use Bigwhoop\SentenceBreaker\Lexing\Lexer;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\WhitespaceToken;
use PHPUnit\Framework\TestCase;

class LexerTest extends TestCase
{
    public function testCompleteSentence(): void
    {
        $text = 'He said: "Hello there!" How are you? Good.';
        $expected = [
            'T_CAPITALIZED_WORD<"He">',
            'T_WORD<"said:">',
            'T_QUOTED_STR<"\"Hello there!\"">',
            'T_CAPITALIZED_WORD<"How">',
            'T_WORD<"are">',
            'T_WORD<"you">',
            'T_QUESTION_MARK<"?">',
            'T_CAPITALIZED_WORD<"Good">',
            'T_PERIOD<".">',
            'T_EOF',
        ];

        $lexer = new Lexer();
        $tokens = $lexer->run($text);

        $actual = $this->getTokenStrings($tokens);

        $this->assertEquals($expected, $actual);
    }

    public function testQuotes(): void
    {
        $text = "the Latin verb \"docēre\" [dɔˈkeːrɛ] 'to teach'. It has";
        $expected = [
            'T_WORD<"the">',
            'T_CAPITALIZED_WORD<"Latin">',
            'T_WORD<"verb">',
            'T_QUOTED_STR<"\"docēre\"">',
            'T_WORD<"[dɔˈkeːrɛ]">',
            'T_QUOTED_STR<"\'to teach\'">',
            'T_PERIOD<".">',
            'T_CAPITALIZED_WORD<"It">',
            'T_WORD<"has">',
            'T_EOF',
        ];

        $lexer = new Lexer();
        $tokens = $lexer->run($text);

        $actual = $this->getTokenStrings($tokens);

        $this->assertEquals($expected, $actual);
    }

    public function testFancyQuotes(): void
    {
        $text = 'the Latin verb ‘docēre’ [dɔˈkeːrɛ] “to teach”. It has';
        $expected = [
            'T_WORD<"the">',
            'T_CAPITALIZED_WORD<"Latin">',
            'T_WORD<"verb">',
            'T_QUOTED_STR<"‘docēre’">',
            'T_WORD<"[dɔˈkeːrɛ]">',
            'T_QUOTED_STR<"“to teach”">',
            'T_PERIOD<".">',
            'T_CAPITALIZED_WORD<"It">',
            'T_WORD<"has">',
            'T_EOF',
        ];

        $lexer = new Lexer();
        $tokens = $lexer->run($text);

        $actual = $this->getTokenStrings($tokens);

        $this->assertEquals($expected, $actual);
    }

    public function testParenthesis(): void
    {
        $text = 'It is (really. really!) important!';
        $expected = [
            'T_CAPITALIZED_WORD<"It">',
            'T_WORD<"is">',
            'T_PARENTHESES_STR<"(really. really!)">',
            'T_WORD<"important">',
            'T_EXCLAMATION_POINT<"!">',
            'T_EOF',
        ];

        $lexer = new Lexer();
        $tokens = $lexer->run($text);

        $actual = $this->getTokenStrings($tokens);

        $this->assertEquals($expected, $actual);
    }

    public function testAbbreviations(): void
    {
        $text = 'Hello Mr. Jones, please turn on the T.V.';
        $expected = [
            'T_CAPITALIZED_WORD<"Hello">',
            'T_CAPITALIZED_WORD<"Mr">',
            'T_PERIOD<".">',
            'T_CAPITALIZED_WORD<"Jones,">',
            'T_WORD<"please">',
            'T_WORD<"turn">',
            'T_WORD<"on">',
            'T_WORD<"the">',
            'T_CAPITALIZED_WORD<"T">',
            'T_PERIOD<".">',
            'T_CAPITALIZED_WORD<"V">',
            'T_PERIOD<".">',
            'T_EOF',
        ];

        $lexer = new Lexer();
        $tokens = $lexer->run($text);

        $actual = $this->getTokenStrings($tokens);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @param Token[] $tokens
     *
     * @return string[]
     */
    private function getTokenStrings(iterable $tokens): array
    {
        $values = [];

        foreach ($tokens as $token) {
            if ($token instanceof WhitespaceToken) {
                continue;
            }

            $value = str_replace('"', '\"', $token->getPrintableValue());

            $values[] = $token->getName().(empty($value) ? '' : '<"'.$value.'">');
        }

        return $values;
    }
}
