<?php


namespace App\Server;


use App\Connection\PeerConnection;
use App\Protocol\SegProtocol;
use Log;
use Wilon\Server;

class WilonServer
{
    /**
     * @var Server
     */
    private $server;

    public function __construct(string $host, int $port)
    {
        $this->server = new Server($host, $port);
        $this->server->set([
            'worker_num' => 8,
        ]);
        $this->handleSocket();
    }

    public function handleSocket(): void
    {
        $handlers = [];
        $this->server->on('connect', function (Server $server, string $peer) use(&$handlers) {
            Log::info($peer, 'connected');
            if (!isset($handlers[$peer])) {
                $handlers[$peer] = new SegProtocol(
                    new PeerConnection($server, $peer)
                );
            }
        });
        $this->server->on('close', function (Server $server, string $peer) use(&$handlers) {
            Log::info($peer, 'closed');
            unset($handlers[$peer]);
        });
        $this->server->on('receive', function (Server $server, string $peer, string $data) use(&$handlers) {
            if (isset($handlers[$peer])) {
                /**@var $handler SegProtocol**/
                $handler = $handlers[$peer];
                $handler->handle($data);
                return;
            }
            $server->close($peer);
        });
    }

    public function start(): void
    {
        $this->server->start();
    }
}