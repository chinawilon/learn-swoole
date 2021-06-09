<?php


namespace App\Server;


use App\Cache;
use App\Connection\SwooleConnection;
use App\Engine;
use App\Processor;
use App\Protocol\StreamProtocol;
use Swoole\Coroutine\Server;
use Swoole\Coroutine\Server\Connection;
use Swoole\Process\Manager;

class ManagerServer
{
    /**
     * @var Manager
     */
    private $pm;

    public function __construct(string $host, int $port, Cache $cache)
    {
        $this->pm = new Manager();

        $this->pm->addBatch(4, function () use($cache) {
            $cache->connect();
            $engine = new Engine(100, new Processor(), $cache);
            $engine->run();
        }, true);

        $this->pm->addBatch(4, function () use($host, $port, $cache) {
            $cache->connect();
            $server = new Server($host, $port, false, true);
            $protocol = new StreamProtocol($cache);
            $server->handle(function (Connection $connection) use($protocol) {
                $protocol->handle(new SwooleConnection($connection));
            });
            $server->start();
        }, true);
    }

    public function start(): void
    {
        $this->pm->start();
    }
}