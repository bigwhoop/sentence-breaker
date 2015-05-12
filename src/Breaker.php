<?php
/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker;

class Breaker
{
    /**
     * @param  string $text
     * @return string[]
     */
    public function getSentences($text)
    {
        $text = str_replace(["\r", "\n", "\t"], ' ', $text);
        while (strpos($text, '  ') !== false) {
            $text = str_replace('  ', ' ', $text);
        }
        
        $abbreviations = explode("\n", file_get_contents(__DIR__ . '/../data/all.txt'));
        $quotedAbbreviations = join("|", array_map('preg_quote', $abbreviations));
        
        $re = <<<RE
/                       # Split sentences on whitespace between them.
(?<=                    # Begin positive lookbehind.
  [.!?]                   # Either an end of sentence punct,
  | [.!?]['"]             # or end of sentence punct and quote.
)                       # End positive lookbehind.
(?<!                    # Begin negative lookbehind.
  $quotedAbbreviations    # Skip these abbreviations
)                       # End negative lookbehind.
\s+                     # Split on whitespace between sentences.
/ix
RE;
        
        return preg_split($re, $text, -1, PREG_SPLIT_NO_EMPTY);
    }
}
