<?php


namespace App\Connection;


use Swoole\Coroutine\Server\Connection;

class SwooleConnection implements ConnectionInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function write(string $msg)
    {
        return $this->connection->send($msg);
    }

    public function read(): string
    {
        return $this->connection->recv() ?? '';
    }

    public function close(): bool
    {
        return $this->connection->close();
    }
}