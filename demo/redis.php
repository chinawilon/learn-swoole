<?php

use App\Lock;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;
use function Co\run;

include __DIR__.'/../vendor/autoload.php';

Log::info('start');
//run(function () {
//    $redis = new Redis();
//    $redis->connect('172.17.0.4', 6379);
//    for ($i = 0; $i < 100; $i++) {
//        go(static function () use($i, $redis) {
//            sleep(1);
//            $redis->rPush('test', $i);
//        });
//    }
//});

//$redis = new Redis();
//$redis->connect('172.17.0.4', 6379);
//
//run(function () use($redis) {
//    for ($i = 0; $i < 100000; $i++) {
//        go(static function () use($i, $redis) {
//            sleep(1);
//            Log::info($redis->rPush('test', $i));
//        });
//    }
//});

//run(function () {
//    $redis = new Redis();
//    $redis->connect('172.17.0.4', 6379);
//    $lock = new Lock();
//    for ($i = 0; $i < 100; $i++) {
//        go(static function () use($i, $redis, $lock) {
//            sleep(1);
//            $lock->lock();
//            Log::info($redis->rPush('test', $i));
//            $lock->unlock();
//        });
//    }
//});

//$pool = new RedisPool((new RedisConfig())->withHost('172.17.0.4')->withPort(6379));
//run(function () use($pool) {
//    for ($i = 0; $i < 100; $i++) {
//        go(static function () use($pool, $i) {
//            $redis = $pool->get();
//            sleep(1);
//            Log::info($redis->rPush('test', $i));
//            $pool->put($redis);
//        });
//    }
//});

Log::info('end');