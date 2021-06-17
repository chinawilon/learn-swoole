<?php


namespace App\Connection;


use Wilon\Server;

class PeerConnection implements ConnectionInterface
{
    /**
     * @var Server
     */
    private $server;
    /**
     * @var string
     */
    private $peer;

    public function __construct(Server $server, string $peer)
    {
        $this->server = $server;
        $this->peer = $peer;
    }

    public function close(): bool
    {
        return $this->server->close($this->peer);
    }

    public function read(): string
    {
        // TODO: Implement read() method.
    }

    public function write(string $msg)
    {
        return $this->server->send($this->peer, $msg);
    }
}