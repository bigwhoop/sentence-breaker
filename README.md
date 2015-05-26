# sentence-breaker

[![Build Status](https://travis-ci.org/bigwhoop/sentence-breaker.svg?branch=master)](https://travis-ci.org/bigwhoop/sentence-breaker)
[![Code Coverage](https://scrutinizer-ci.com/g/bigwhoop/sentence-breaker/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bigwhoop/sentence-breaker/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bigwhoop/sentence-breaker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bigwhoop/sentence-breaker/?branch=master)

Sentence boundary disambiguation (SBD) - or sentence breaking - library written in PHP.

## Installation

    composer require "bigwhoop/sentence-breaker":"~2.0"

## Usage

    <?php
    use Bigwhoop\SentenceBreaker\SentenceBreaker;
    
    $breaker = new SentenceBreaker();
    $breaker->addAbbreviations(['Dr', 'Prof']);
    
    $sentences = $breaker->split("Hello Dr. Jones! How are you? I'm fine, thanks!");
    // ['Hello Dr. Jones!', 'How are you?', "I'm fine, thanks!"]

### Rules

By default the `rules/rules.ini` file is loaded. Its format is a list of patterns ...

    TOKEN [... TOKEN] = PROBABILITY
    T_CAPITALIZED_WORD <T_PERIOD> T_WHITESPACE T_CAPITALIZED_WORD = 75

The token enclosed in `<` / `>` is the one that defines for which token the pattern is applied. The example pattern
 above would be applied to each `T_PERIOD` token found in the input data. The probability defines how likely a sentence
 boundary is **after** this token.

So for this pattern to match, the input text would need to contain something along the lines of *This is **Waldo.
 He** likes dogs.*.

The available tokens are:

Token | Description | Example
--- | --- | ---
`T_WORD` | A non-capitalized word. | `hello`, `world`
`T_CAPITALIZED_WORD` | A capitalized word. | `Hello`, `World`
`T_EOF` | The end of the input. | -
`T_PERIOD` | A period. | `.`
`T_EXCLAMATION_POINT` | An exclamation point. | `!`
`T_QUESTION_MARK` | A question mark. | `?`
`T_QUOTED_STR` | A string enclosed in single or double quotes | `"Hello world!"`, `'Hello world...'`
`T_WHITESPACE` | Whitespace characters like spaces, LF, CR. | -
`T_ABBREVIATION` | An abbreviation without the trailing period. | `Dr`, `Prof`

**TIP:** You can add your own rules via `$breaker->addRules()`.


### Abbreviation Providers

Inside the `data` directory are flat files containing abbreviations (in English), collected from various
 sources. They can be loaded like this:

    use Bigwhoop\SentenceBreaker\Abbreviations\FlatFileProvider;
    
    // Load legal.txt and biz.txt
    $breaker->addAbbreviations(new FlatFileProvider('/path/to/data/directory', ['legal', 'biz']));
    
    // Load all files
    $breaker->addAbbreviations(new FlatFileProvider('/path/to/data/directory', ['*']));

To make it fast and easy, all abbreviations are available in the `all.txt` file. You can load it like this:

    $breaker->addAbbreviations(new FlatFileProvider('/path/to/data/directory', ['all']));
    

## How does it work?

The input text is run through a lexer.

> In computer science, lexical analysis is the process of converting a sequence of characters into a sequence
> of tokens, i.e. meaningful character strings.

So for example `He asked: "What's on TV?" On T.V.? I have no clue. Really!` would result in the following sequence
 of tokens:

    "He" "asked:" T_QUOTED_STR "On" "T.V" T_PERIOD T_QUESTION_MARK
    "I" "have" "no" "clue" T_PERIOD "Really" T_EXCLAMATION_POINT

This sequence of tokens is then run through a probability calculator that calculates for each token the probability
 of it being the boundary of a sentence. The calculator uses rules that are matched against each token. For example
 if a T_EXCLAMATION_POINT is followed by a capitalized string the chance of it being a sentence boundary is 100%.

In the end the tokens are re-assembled into the sentences. The user can choose which threshold he wants to apply
 when starting new sentences. For example the probability must be greater or equal to 50% that a boundary was
 detected.

## TODO

- [X] `calculateCurrentTokenProbability` is a big mess. Let's split it up into multiple *Rule* classes. Maybe use a rules engine.
- [ ] Add abbreviations support for different languages.

## License

MIT. See LICENSE file.