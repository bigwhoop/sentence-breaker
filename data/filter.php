<?php

declare(strict_types=1);

foreach (glob(__DIR__ . '/*.txt') as $file) {
    $data = file_get_contents($file);
    $data = str_replace("\r", '', $data);

    $lines = explode("\n", $data);

    $lines = array_map(static function ($line) {
        [$line] = explode('--', $line);

        return trim($line);
    }, $lines);

    $lines = array_filter($lines, static function ($line) {
        return '.' === substr($line, -1);
    });

    $lines = array_unique($lines);
    asort($lines);

    $data = join("\n", $lines);

    file_put_contents($file, $data);

    echo $file . " ... DONE\n";
}


echo "DONE.\n";
