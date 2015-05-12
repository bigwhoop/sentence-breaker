<?php
@unlink(__DIR__ . '/all.txt');

$lines = [];
foreach (glob(__DIR__ . '/*.txt') as $file) {
    $data = file_get_contents($file);
    $lines = array_merge($lines, explode("\n", $data));
}
    
$lines = array_unique($lines);
asort($lines);

$data = join("\n", $lines);
file_put_contents(__DIR__ . '/all.txt', $data);


echo "DONE.\n";