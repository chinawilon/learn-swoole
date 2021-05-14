<?php


use App\Cache;
use App\Engine;
use App\Processor;
use App\Protocol;
use Swoole\Coroutine\Server\Connection;
use Swoole\Process\Manager;

include __DIR__.'/../vendor/autoload.php';


$pm = new Manager();
$cache = new Cache('172.17.0.4', 6379);

$pm->addBatch(4, function () use($cache) {
    $cache->connect();
    $engine = new Engine(100, new Processor(), $cache);
    $engine->run();
}, true);

$pm->addBatch(4, function () use($cache) {
    $cache->connect();
    $server = new Swoole\Coroutine\Server('0.0.0.0', 8080, false, true);
    $protocol = new Protocol($cache);
    $server->handle(function (Connection $connection) use($protocol) {
        $protocol->handle($connection);
    });
    $server->start();
}, true);

$pm->start();
