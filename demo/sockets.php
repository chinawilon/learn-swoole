<?php

use Swoole\Process\Manager;

include __DIR__.'/../bootstrap/app.php';

$sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_DGRAM, IPPROTO_IP);

$pm = new Manager();

$pm->addBatch(2, function ()  use($sockets) {
    fclose($sockets[1]);
    while ($data = stream_get_line($sockets[0], 1024, "\n")) {
//        if ( $data !== 'abcdefghijklmnopqrstuvwxyz') {
            go(static function () use($data) {
               Log::info($data);
            });
//        }
    }
}, true);

// PIPE_BUF

$pm->addBatch(2, function () use($sockets) {
    fclose($sockets[0]);
    for (;;) {
        fwrite($sockets[1], "abcdefghijklmnopqrstuvwxyz\n");
    }
});

$pm->start();
