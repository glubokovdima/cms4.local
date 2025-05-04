<?php
$dir = __DIR__ . '/blocks';
foreach (glob($dir . '/*.php') as $file) {
    echo basename($file) . "\n";
}