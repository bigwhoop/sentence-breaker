<?php

declare(strict_types=1);

/**
 * Exports data from Oxford English Dictionary.
 *
 * @see http://public.oed.com/how-to-use-the-oed/abbreviations/
 */
require __DIR__ . '/../vendor/autoload.php';

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client();
$crawler = $client->request('GET', 'http://public.oed.com/how-to-use-the-oed/abbreviations/');

$abbrs = [];

$crawler->filter('.page-content table tbody td:first-child')->each(function (Crawler $td) use (&$abbrs) {
    $value = trim($td->text());
    if ('.' === substr($value, -1)) {
        $abbrs[] = $value;
    }
});

$abbrs = array_unique($abbrs);

file_put_contents(__DIR__ . '/oed.txt', join("\n", $abbrs));

echo "DONE.\n";
