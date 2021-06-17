<?php

declare(ticks=1);

include __DIR__.'/../bootstrap/app.php';

$running = true;
//pcntl_signal(SIGCHLD, function () {
//    Log::info('sigchld');
//});
//
pcntl_signal(SIGTERM, function ($signo, $siginfo) use(&$running) {
    Log::info($siginfo);
    $running = false;
    Log::info('sigterm');
});

// ctrl+D
//while (! feof(STDIN)) {
//    Log::info(stream_get_line(STDIN, 1024, "\n"));
//}

// ctrl+\
//pcntl_signal(SIGQUIT, function () {
//    Log::info('sigquit');
//});

// ctrl+C
//pcntl_signal(SIGINT, function () {
//    Log::info('sigint');
//});

// sigtstp
//pcntl_signal(SIGTSTP, function () {
//    Log::info('sigtstp');
//});

// sigkill sigstop 不能捕获的
// sigcont


//
//pcntl_signal(SIGUSR1, function () {
//    Log::info('sigusr1');
//});
//
//pcntl_signal(SIGUSR1, function () {
//    Log::info('sigusr2');
//});
//
while ($running) {
    sleep(1);
}


//\Co\run(function () {
//    \Swoole\Process::signal(SIGTERM, function () {
//        Log::info('sigterm');
//    });
//
//    for (;;) {
//        sleep(1);
//    }
//});
