<?php


namespace App\Server;


use App\Connection\PHPConnection;
use App\Protocol\SegProtocol;
use Log;
use RuntimeException;
use Swoole\Event;

class EpollServer
{
    /**
     * @var false|resource
     */
    private $server;

    public function __construct(string $host, int $port)
    {
        $this->server = stream_socket_server("tcp://$host:$port", $errno, $errstr);
        if (! $this->server ) {
            Log::error("stream_socket_server error", $errno, $errstr);
            throw new RuntimeException("stream_socket_server error");
        }
    }

    public function start(): void
    {
        $handlers = [];
        Event::add($this->server, function () use(&$handlers) {
            if ($conn = @stream_socket_accept($this->server,  -1, $peer)) {
                Log::info($peer, 'connected');
                Event::add($conn, function () use($conn, $peer, &$handlers) {
                    if (feof($conn)) {
                        Log::info($peer, 'closed');
                        unset($handlers[$peer]);
                        Event::del($conn);
                        return;
                    }
                    if ($data = fread($conn, 1024)) {
                        if (!isset($handlers[$peer])) {
                            $handlers[$peer] = new SegProtocol(new PHPConnection($conn));
                        }
                        $handler = $handlers[$peer];
                        $handler->handle($data);
//                        $event->dispatch('onReceive', $data);
                    }
                });
            }
        });
        Log::info('wait');
        Event::wait();
    }
}