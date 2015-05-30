<?php

/**
 * This file is part of sentence-breaker.
 *
 * (c) Philippe Gerber
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bigwhoop\SentenceBreaker\Tests;

use Bigwhoop\SentenceBreaker\Abbreviations\FlatFileProvider;
use Bigwhoop\SentenceBreaker\SentenceBreaker;

class SentenceBreakerTest extends \PHPUnit_Framework_TestCase
{
    public function testSplitting()
    {
        $breaker = new SentenceBreaker();
        $breaker->addAbbreviations(['Dr', 'Prof']);

        $sentences = $breaker->split("Hello Dr. Jones! How are you? I'm fine, thanks!");
        $this->assertSame(['Hello Dr. Jones!', 'How are you?', "I'm fine, thanks!"], $sentences);
    }

    /**
     * @dataProvider dataSentences
     *
     * @param string $text
     * @param array  $sentences
     */
    public function testSplittingWithFlatFileProvider($text, array $sentences)
    {
        $breaker = new SentenceBreaker();
        $breaker->addAbbreviations(new FlatFileProvider(__DIR__.'/../assets/data', ['*']));

        $this->assertSame($sentences, $breaker->split($text));
    }

    /**
     * @return array
     */
    public function dataSentences()
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
        ];
    }
}
