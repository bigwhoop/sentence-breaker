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

        foreach ($tokenProbabilities as $idx => $tokenProbability) {
            $token = $tokenProbability->getToken();

            $sentenceIdx = count($sentences) - 1;
            $sentences[$sentenceIdx] .= $token->getPrintableValue();

            $meetsThreshold = $tokenProbability->getProbability() >= $threshold;
            $currentSentenceIsEmpty = empty(trim($sentences[$sentenceIdx]));

            if ($meetsThreshold && !$currentSentenceIsEmpty) {
                $sentences[] = '';
            }
        }

        if ('' === $sentences[count($sentences) - 1]) {
            unset($sentences[count($sentences) - 1]);
        }

        $sentences = array_map('ltrim', $sentences);

        return $sentences;
    }
}
