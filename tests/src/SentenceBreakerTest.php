<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Tests;

use Bigwhoop\SentenceBreaker\Abbreviations\FlatFileProvider;
use Bigwhoop\SentenceBreaker\SentenceBreaker;
use PHPUnit\Framework\TestCase;

class SentenceBreakerTest extends TestCase
{
    public function testSplitting(): void
    {
        $breaker = new SentenceBreaker();
        $breaker->addAbbreviations(['Dr', 'Prof']);

        $sentences = $breaker->split("Hello Dr. Jones! How are you? I'm fine, thanks!");

        $this->assertSame(['Hello Dr. Jones!', 'How are you?', "I'm fine, thanks!"], iterator_to_array($sentences));
    }

    public function testPluralizedAbbreviation(): void
    {
        $breaker = new SentenceBreaker();

        $sentences = $breaker->split("So it looks like they've got F.D.R.'s for One Marine right now.");
        $this->assertSame(["So it looks like they've got F.D.R.'s for One Marine right now."], iterator_to_array($sentences));
    }

    /**
     * @dataProvider dataSentences
     * @param array<string> $sentences
     */
    public function testSplittingWithFlatFileProvider(string $text, array $sentences): void
    {
        $breaker = new SentenceBreaker();
        $breaker->addAbbreviations(new FlatFileProvider(__DIR__.'/../assets/data', ['*']));

        $this->assertSame($sentences, iterator_to_array($breaker->split($text)));
    }

    /**
     * @return array<mixed>
     */
    public function dataSentences(): array
    {
        return [
            [
                "Hello Dr. Jones! How are you? I'm fine, thanks!",
                ['Hello Dr. Jones!', 'How are you?', "I'm fine, thanks!"],
            ],
            [
                'Doctor, as a title, originates from the Latin word of the same spelling and meaning. The word is '.
                "originally an agentive noun of the Latin verb docēre [dɔˈkeːrɛ] 'to teach'. It has been used as ".
                'an honored academic title for over a millennium in Europe, where it dates back to the rise of the '.
                'first universities. This use spread to the Americas, former European colonies, and is now prevalent '.
                'in most of the world. Contracted "Dr" or "Dr.", it is used as a designation for a person who has '.
                'obtained a doctorate-level degree. Doctorates may be research doctorates or professional doctorates.',
                [
                    'Doctor, as a title, originates from the Latin word of the same spelling and meaning.',
                    "The word is originally an agentive noun of the Latin verb docēre [dɔˈkeːrɛ] 'to teach'.",
                    'It has been used as an honored academic title for over a millennium in Europe, where it dates back to the rise of the first universities.',
                    'This use spread to the Americas, former European colonies, and is now prevalent in most of the world.',
                    'Contracted "Dr" or "Dr.", it is used as a designation for a person who has obtained a doctorate-level degree.',
                    'Doctorates may be research doctorates or professional doctorates.',
                ],
            ],
            [
                'He said: ‘Look at me, I am fancy.’ and the other replied “You really are!” True story ...',
                [
                    'He said: ‘Look at me, I am fancy.’ and the other replied “You really are!”',
                    'True story ...'
                ],
            ],
            [
                'Currently I am storing my bottles in the crates at a (about) 20 degree angle (bottles are upwards!).',
                [
                    'Currently I am storing my bottles in the crates at a (about) 20 degree angle (bottles are upwards!).',
                ]
            ],
        ];
    }
}
