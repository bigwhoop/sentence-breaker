<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

interface PotentialAbbreviationToken
{
    public function getValue(): string;
}
