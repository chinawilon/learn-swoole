<?php

use Swoole\Process\Manager;

include __DIR__.'/../bootstrap/app.php';

$fifo = RUNTIME_PATH.'/fifo';

if (! file_exists($fifo) ) {
    posix_mkfifo($fifo, 0600);
}

$pm = new Manager();
$lock = new \Swoole\Lock();
$pm->addBatch(2, function (\Swoole\Process\Pool $pool)  use($fifo, $lock) {
    $f = fopen($fifo, "rb");
    $lock->lock();
    while ($data = stream_get_line($f, 1024, "\n")) {
        if ( $data !== 'abcdefghijklmnopqrstuvwxyz') {
            Log::error($pool->getProcess()->pid, '=>', $data);
        }
    }
    $lock->unlock();
});

//PIPE_BUF

$pm->addBatch(100, function () use($fifo) {
    $f = fopen($fifo, "wb");
    for (;;) {
        fwrite($f, "abcdefghijklmnopqrstuvwxyz\n");
    }
});
$pm->start();
