<?php


use App\Cache;
use function Co\run;

include __DIR__.'/../vendor/autoload.php';

//
//run(function () {
//    $cache = new Cache('172.17.0.5', 6379);
////$server = new \App\Server\ManagerServer('0.0.0.0', 8080, $cache);
//    $server = new \App\Server\SocketServer('0.0.0.0', 8080, $cache);
//    $server->start();
//});


$cache = new Cache('172.17.0.5', 6379);
//$server = new \App\Server\ManagerServer('0.0.0.0', 8080, $cache);
$server = new \App\Server\PHPServer('0.0.0.0', 8080);
$server->start();