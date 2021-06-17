<?php

use Wilon\Server;

include __DIR__.'/../bootstrap/app.php';

//$pm = new \Wilon\Manager();
//$pm->addBatch(4, function ($workerId) {
//    for (;;) {
//        sleep(1);
//    }
//});
//$pm->start();

$server = new Server('0.0.0.0', 8080);

$server->set(['worker_num'=>4]);

$server->on('connect', function (Server $server, string $peer) {
    Log::info($peer, 'connected');
});
$server->on('close', function (Server $server, string $peer) {
    Log::info($peer, 'closed');
});
$server->on('receive', function (Server $server, string $peer, $data) {
    Log::info($peer, $data);
});

$server->start();