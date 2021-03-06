<?php
declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker;

class SentenceBuilder
{
    /**
     * @param TokenProbability[] $tokenProbabilities
     * @param int                $threshold
     *
     * @return array
     */
    public function build(array $tokenProbabilities, int $threshold = 50): array
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
