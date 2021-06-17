<?php

//declare(ticks=1);

include __DIR__.'/../bootstrap/app.php';
//
//pcntl_signal(SIGTERM, function () {
//   Log::info(getmypid(), 'sigterm');
//});
//
//$pid = pcntl_fork();
//
//if ( $pid < 0 ) {
//    Log::error('fork error');
//    return;
//}
//
//// children process
//if ( $pid === 0 ) {
////    pcntl_signal(SIGTERM, SIG_DFL);
//    pcntl_signal(SIGTERM, SIG_IGN);
//    for (;;) {
//        sleep(1);
//    }
//    exit(0);
//}
//
//pcntl_wait($status); // blocking -1 / 0 /pid


//$process = new \Swoole\Process(function () {
//    for (;;) {
//        sleep(1);
//    }
//});
//$process->start();
//sleep(10);

//$pool = new \Swoole\Process\Pool(2, null, null, true);
//$pool->on('WorkerStart', function () {
//    \Swoole\Runtime::enableCoroutine(true);
//    \Swoole\Process::signal(SIGTERM, function () {
//        Log::info('sigterm');
//    });
//    for (;;) {
//        sleep(1);
//    }
//});
//$pool->start();

//$pm = new \Swoole\Process\Manager();
//
//$pm->addBatch(2, function () {
//    \Swoole\Process::signal(SIGTERM, function () {
//        Log::info('sigterm');
//    });
//    for (;;) {
//        sleep(1);
//    }
//}, true);
//
//$pm->start();
