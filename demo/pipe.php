<?php

include __DIR__.'/../vendor/autoload.php';

$reader = __DIR__.'/reader.php';
$pipe = popen("php $reader", "wr");

for (;;) {
    fwrite($pipe, "abcdefghijklmnopqrstuvwxyz\n");
    Log::info(fread($pipe, 1024));
}