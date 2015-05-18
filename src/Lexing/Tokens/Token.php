<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Lexing\Tokens;

interface Token
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPrintableValue();
}
