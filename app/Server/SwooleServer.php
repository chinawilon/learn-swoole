<?php


namespace App\Server;


use App\Connection\FdConnection;
use App\Protocol\SegProtocol;
use Log;
use Swoole\Server;

class SwooleServer
{
    /**
     * @var Server
     */
    private $server;

    public function __construct(string $host, int $port)
    {
        $this->server = new Server($host, $port, SWOOLE_PROCESS);
        $this->server->set([
            'worker_num' => 1,
        ]);
        $this->handleSocket();
    }

    public function handleSocket(): void 
    {
        $handlers = [];
        $this->server->on('connect', function (Server $server, int $fd) use(&$handlers) {
            $peer = $server->getClientInfo($fd);
            Log::info($peer['remote_ip'].':'.$peer['remote_port'], 'connected');
            if (!isset($handlers[$fd])) {
                $handlers[$fd] = new SegProtocol(
                    new FdConnection($server, $fd)
                );
            }
        });
        $this->server->on('close', function (Server $server, int $fd) use(&$handlers) {
            $peer = $server->getClientInfo($fd);
            Log::info($peer['remote_ip'].':'.$peer['remote_port'], 'closed');
            unset($handlers[$fd]);
        });
        $this->server->on('receive', function (Server $server, int $fd, int $reactorId, string $data) use(&$handlers) {
            if (isset($handlers[$fd])) {
                /**@var $handler SegProtocol**/
                $handler = $handlers[$fd];
                $handler->handle($data);
                return;
            }
            $server->close($fd, true);
        });
    }

    public function start(): void
    {
        $this->server->start();
    }
}