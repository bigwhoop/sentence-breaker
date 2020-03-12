<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

interface Token
{
    public function getName(): string;

    public function getPrintableValue(): string;
}
