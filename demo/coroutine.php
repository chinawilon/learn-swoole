<?php


use Swoole\Coroutine\WaitGroup;
use function Co\run;

include __DIR__.'/../vendor/autoload.php';

Log::info('start');

run(function () {
    Log::info('run start');
    $wg = new WaitGroup();
    $result = [];
    for ($i = 0; $i < 100; $i++) {
        $wg->add();
        go(static function () use($i, &$result, $wg){
            sleep(1);
            $result[] = $i;
            $wg->done();
        });
    }
    $wg->wait();
    Log::info($result);
    Log::info('run end');
});


Log::info('end');