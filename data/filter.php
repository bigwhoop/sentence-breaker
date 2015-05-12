<?php
foreach (glob(__DIR__ . '/*.txt') as $file) {
    $data = file_get_contents($file);
    $data = str_replace("\r", '', $data);
    
    $lines = explode("\n", $data);
    $lines = array_map(function($line) {
        list($line) = explode('--', $line);
        return trim($line);
    }, $lines);
    
    $data = join("\n", $lines);
    
    file_put_contents($file, $data);
    
    echo $file . " ... DONE\n";
}


echo "DONE.\n";