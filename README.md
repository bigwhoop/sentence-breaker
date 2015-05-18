# sentence-breaker (WIP)

[![Build Status](https://travis-ci.org/bigwhoop/sentence-breaker.svg?branch=master)](https://travis-ci.org/bigwhoop/sentence-breaker)
[![Code Coverage](https://scrutinizer-ci.com/g/bigwhoop/sentence-breaker/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bigwhoop/sentence-breaker/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bigwhoop/sentence-breaker/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bigwhoop/sentence-breaker/?branch=master)

Sentence boundary disambiguation (SBD) - or sentence breaking - library written in PHP.

## Installation

    composer require "bigwhoop/sentence-breaker":"~1.0"

## Usage

    <?php
    use Bigwhoop\SentenceBreaker\SentenceBreaker;
    
    $breaker = new SentenceBreaker();
    $breaker->addAbbreviations(['Mr', 'Mrs', 'Ms', 'Dr', 'Prof');
    
    $sentences = $breaker->split("Welcome! How are you? I'm fine, thanks!");
    // ['Welcome!', 'How are you?', 'I'm fine, thanks!']

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

## License

MIT. See LICENSE file.