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

use Bigwhoop\SentenceBreaker\BoundaryProbabilityCalculation\TokenProbability;
use Bigwhoop\SentenceBreaker\Lexing\Tokens\Token;

class SentenceBuilder
{
    /**
     * @param TokenProbability[] $tokenProbabilities
     * @param int                $threshold
     *
     * @return array
     */
    public function build(array $tokenProbabilities, $threshold = 50)
    {
        $sentences = [''];

        $numTokens = count($tokenProbabilities);

        foreach ($tokenProbabilities as $idx => $tokenProbability) {
            $token = $tokenProbability->getToken();

            $sentenceIdx = count($sentences) - 1;
            $sentences[$sentenceIdx] .= $token instanceof Token ? $token->getPrintableValue() : $token;

            $isLastToken = $idx + 1 === $numTokens;
            $meetsThreshold = $tokenProbability->getProbability() >= $threshold;
            $currentSentenceIsEmpty = empty(trim($sentences[$sentenceIdx]));

            if ($meetsThreshold && !$isLastToken && !$currentSentenceIsEmpty) {
                $sentences[] = '';
            }
        }

        $sentences = array_map('ltrim', $sentences);

        return $sentences;
    }
}
