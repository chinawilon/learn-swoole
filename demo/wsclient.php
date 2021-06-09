<?php

use Swoole\Timer;
use Swoole\WebSocket\CloseFrame;
use Swoole\WebSocket\Frame;
use function Co\run;

include __DIR__.'/../bootstrap/app.php';

run(function () {
    $client = new Swoole\Coroutine\Http\Client('0.0.0.0', 9090);
    $client->upgrade('/websocket');
//    Log::info('start');
//    for ($i = 0; $i < 10000; $i++) {
//        $client->push($i);
//    }
//    Log::info('end');
//    sleep(65);
//    Log::info($client->recv());
//    Log::info($client->push('abc'));

    $timer = Timer::tick(5000, function () use($client) {
        $ping = new Frame();
        $ping->opcode = WEBSOCKET_OPCODE_PING;
        $client->push($ping);
    });

    // clear timer
    defer(static function () use($timer) {
        Timer::clear($timer);
    });

    for (;;) {
        /**@var $frame Frame **/
        if ((!$frame = $client->recv()) || $frame instanceof CloseFrame) {
            $client->close();
            // connection closed
            break;
        }
        // pong frame
        if ( $frame->opcode === WEBSOCKET_OPCODE_PONG) {
            Log::info('pong');
            continue;
        }
        // data
        Log::info($frame->data);
    }

/**
 *
 * go pseudocode
 *
 * done := make(chan string)
 * timer := time.NewTicker(30 * time.Second)
 * defer timer.Stop()
 *
 * select {
 *  case <- time.C:
 *       // send ping frame
 *  case <- done:
 *      // send other thing
 * }
 */

});