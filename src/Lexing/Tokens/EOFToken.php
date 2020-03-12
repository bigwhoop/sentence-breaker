<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

class EOFToken implements Token
{
    public function getName(): string
    {
        return 'T_EOF';
    }
    
    public function getPrintableValue(): string
    {
        return '';
    }
}
