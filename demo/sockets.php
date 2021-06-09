<?php

use Swoole\Process\Manager;

include __DIR__.'/../bootstrap/app.php';

$sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_DGRAM, IPPROTO_IP);

$pm = new Manager();

// full duplex - sockets / unix socket
// half duplex - pipe / fifo
// simplex

$pm->addBatch(2, function ()  use($sockets) {
    while ($data = fgets($sockets[0], 1024)) {
        fwrite($sockets[1], $data);
    }
}, true);

// PIPE_BUF

$pm->addBatch(2, function () use($sockets) {
    for (;;) {
        fwrite($sockets[1], "abcdefghijklmnopqrstuvwxyz\n");
        Log::info(stream_get_line($sockets[0], 1024, "\n"));
    }
});

$pm->start();
