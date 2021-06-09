<?php


namespace App\Connection;


use Swoole\Server;

class FdConnection implements ConnectionInterface
{
    /**
     * @var Server
     */
    private $server;
    /**
     * @var int
     */
    private $fd;

    public function __construct(Server $server, int $fd)
    {
        $this->server = $server;
        $this->fd = $fd;
    }

    public function read(): string
    {
        // TODO: Implement read() method.
    }

    public function write(string $msg)
    {
        return $this->server->send($this->fd, $msg);
    }

    public function close()
    {
        return $this->server->close($this->fd, true);
    }
}