<?php


namespace App;


use Swoole\Coroutine\Server\Connection;

class BuffIO
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $left = '';

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function read(int $n): string
    {
        for (;;) {
            if (strlen($this->left) >= $n) {
                $msg = substr($this->left, 0, $n);
                $this->left = substr($this->left, $n);
                return $msg;
            }
            if (! $payload = $this->connection->recv() ) {
                return '';
            }
            $this->left .= $payload;
        }
    }

    public function write(string $msg): void
    {
        $this->left .= $msg;
    }

    public function flush()
    {
        $msg = $this->left;
        $this->left = '';
        return $this->connection->send($msg);
    }
}