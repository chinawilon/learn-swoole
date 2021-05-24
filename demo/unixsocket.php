<?php


use Swoole\Lock;
use Swoole\Process\Manager;

include __DIR__.'/../bootstrap/app.php';

$sock = RUNTIME_PATH.'/sock';

$pm = new Manager();
//unix:///mysql.sock
$lock = new Lock(Lock::MUTEX);
$lock->lock();
$pm->add(function () use($sock, $lock) {
    if ( file_exists($sock) ) {
        unlink($sock);
    }
    // socket bind listen
    $server = stream_socket_server("unix://$sock", $errno, $errstr);
    $lock->unlock();
    if (! $server ) {
        Log::error("stream_socket_server error", $errno, $errstr);
        throw new RuntimeException("stream_socket_server error");
    }

    for (;;) {
        $conn = @stream_socket_accept($server, -1);
        while ($data = stream_get_line($conn, 1024, "\n")) {
            Log::info($data);
        }
    }
});

$pm->add(function () use($sock, $lock) {
    $lock->lock();
    $client = @stream_socket_client("unix://$sock", $errno, $errstr);
    $lock->unlock();
    if (! $client ) {
        Log::error("stream_socket_client error", $errno, $errstr);
        sleep(10);
        throw new RuntimeException("stream_socket_client error");
    }
    for (;;) {
        fwrite($client, "abcdefghijklmnopqrstuvwxyz\n");
    }
});

$pm->start();