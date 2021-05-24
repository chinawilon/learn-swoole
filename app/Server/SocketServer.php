<?php


namespace App\Server;


use App\Cache;
use App\Connection\PHPConnection;
use App\Protocol;
use Log;
use RuntimeException;

class SocketServer
{
    /**
     * @var false|resource
     */
    private $server;
    /**
     * @var Protocol
     */
    private $protocol;

    public function __construct(string $host, int $port, Cache $cache)
    {
        $cache->connect();
        $this->protocol = new Protocol($cache);
        $this->server = stream_socket_server("tcp://$host:$port", $errno, $errstr);
        if (! $this->server ) {
            Log::error("stream_socket_server error", $errno, $errstr);
            throw new RuntimeException("stream_socket_server error");
        }
    }

    public function start(): void
    {
        for (;;) {
            $conn = stream_socket_accept($this->server, -1);
            go(function () use($conn) {
                $this->protocol->handle(new PHPConnection($conn));
            });
        }
    }

}