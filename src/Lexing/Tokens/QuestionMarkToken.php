<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class QuestionMarkToken implements Token
{
    public function getName(): string
    {
        return 'T_QUESTION_MARK';
    }

    public function getPrintableValue(): string
    {
        return '?';
    }
}
