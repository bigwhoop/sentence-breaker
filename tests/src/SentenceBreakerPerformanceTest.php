<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Tests;

use Bigwhoop\SentenceBreaker\SentenceBreaker;
use PHPUnit\Framework\TestCase;

class SentenceBreakerPerformanceTest extends TestCase
{
    private const MAX_SENTENCES = 1000;

    private const SENTENCES = [
        'Doctor, as a title, originates from the Latin word of the same spelling and meaning.',
        "The word is originally an agentive noun of the Latin verb docēre [dɔˈkeːrɛ] 'to teach'.",
        'It has been used as an honored academic title for over a millennium in Europe, where it dates back to the rise of the first universities.',
        'This use spread to the Americas, former European colonies, and is now prevalent in most of the world.',
        'Contracted "Dr" or "Dr.", it is used as a designation for a person who has obtained a doctorate-level degree.',
        'Doctorates may be research doctorates or professional doctorates.',
    ];

    public function testCanAccessTheFirstSentenceOfLargeText(): void
    {
        $exampleSentences = $this->generateSentencesInRandomOrder(self::MAX_SENTENCES);
        $firstSentence = $exampleSentences[0];
        $text = implode(' ', $exampleSentences);
        unset($exampleSentences);

        $breaker = new SentenceBreaker();
        $breaker->addAbbreviations(['Dr', 'Prof']);

        $sentences = $breaker->split($text);

        foreach ($sentences as $sentence) {
            // stop at first iteration so this test can be used
            // with the old version that returned an array and
            // the new version that returns a generator
            self::assertSame($firstSentence, $sentence);
            break;
        }
    }

    /**
     * @return array<string>
     */
    private function generateSentencesInRandomOrder(int $max): array
    {
        $sentenceCount = count(self::SENTENCES);
        $exampleSentences = [];
        foreach (range(1, $max) as $ignored) {
            $exampleSentences[] = self::SENTENCES[random_int(0, $sentenceCount - 1)];
        }

        return $exampleSentences;
    }
}
