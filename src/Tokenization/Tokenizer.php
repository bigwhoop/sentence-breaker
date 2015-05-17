<?php
/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tokenization;

class Tokenizer
{
    private static $defaultToken = WordToken::class;
    
    /** @var array */
    private static $tokens = [
        '.'   => PeriodToken::class,
        '?'   => QuestionMarkToken::class,
        '!'   => ExclamationPointToken::class,
        '."'  => QuotedPeriodToken::class,
        '?"'  => QuotedQuestionMarkToken::class,
        '!"'  => QuotedExclamationPointToken::class,
        ".'"  => SingleQuotedPeriodToken::class,
        "?'"  => SingleQuotedQuestionMarkToken::class,
        "!'"  => SingleQuotedExclamationPointToken::class,
        '...' => ThreePeriodsToken::class,
        '…'   => EllipsisToken::class,
    ];
}
