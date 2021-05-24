<?php


namespace App\Server;

use App\Handler;
use Log;
use RuntimeException;

class PHPServer
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
        $connections = [];
        $handlers = [];
        for (;;) {
            if ($conn = @stream_socket_accept($this->server, empty($connections) ? -1 : 0, $peer)) {
                stream_set_blocking($conn, false);
                Log::info($peer, 'connected');
                $connections[$peer] = $conn;
            }
            $readers = $connections;
            $writers = null;
            $except = null;
            if (@stream_select($readers, $writers, $except, 0, 0)) {
                foreach ($connections as $conn) {
                    $peer = stream_socket_get_name($conn, true);
                    if (feof($conn)) {
                        Log::info($peer, 'closed');
                        unset($connections[$peer], $handlers[$peer]);
                        continue;
                    }
                    if ($data = fread($conn, 1024)) {
                        if (!isset($handlers[$peer])) {
                            $handlers[$peer] = new Handler($conn);
                        }
                        $handler = $handlers[$peer];
                        $handler->handle($data);
                    }
                }
            }
        }
    }
}