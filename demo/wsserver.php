<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\CloseFrame;
use Swoole\Coroutine\Http\Server;
use Swoole\WebSocket\Frame;
use function Swoole\Coroutine\run;

include __DIR__.'/../bootstrap/app.php';

run(function () {
    $server = new Server('0.0.0.0', 9090, false);
    $server->set(['open_websocket_ping_frame' => false]);
    $server->handle('/websocket', function (Request $request, Response $ws) {
        $ws->upgrade();
        while (true) {
            /**@var $frame Frame**/
            $frame = $ws->recv();
            if ($frame === '') {
                $ws->close();
                break;
            }

            if ($frame === false) {
                echo 'errorCode: ' . swoole_strerror(swoole_last_error()) . "\n";
                $ws->close();
                break;
            }

            if ($frame->data === 'close' || get_class($frame) === CloseFrame::class) {
                $ws->close();
                break;
            }

            // ping/pong frame
            if ($frame->opcode === WEBSOCKET_OPCODE_PING ) {
                Log::info('ping');
                $pong = new Frame();
                $pong->opcode = SWOOLE_WEBSOCKET_OPCODE_PONG;
                $ws->push($pong);
                continue;
            }

            Log::info($frame);
//            $ws->push("Hello {$frame->data}!");
//            $ws->push("How are you, {$frame->data}?");
        }
    });

    $server->handle('/', function (Request $request, Response $response) {
        $response->end(<<<HTML
    <h1>Swoole WebSocket Server</h1>
    <script>
var wsServer = 'ws://127.0.0.1:8080/websocket';
var websocket = new WebSocket(wsServer);
websocket.onopen = function (evt) {
    console.log("Connected to WebSocket server.");
    websocket.send('hello');
};

websocket.onclose = function (evt) {
    console.log("Disconnected");
};

websocket.onmessage = function (evt) {
    console.log('Retrieved data from server: ' + evt.data);
};

websocket.onerror = function (evt, e) {
    console.log('Error occured: ' + evt.data);
};
</script>
HTML
        );
    });

    $server->start();
});