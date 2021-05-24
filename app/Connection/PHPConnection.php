<?php


namespace App\Connection;


class PHPConnection implements ConnectionInterface
{
    /**
     * @var resource
     */
    private $connection;

    /**
     * PHPConnection constructor.
     *
     * @param $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function write(string $msg)
    {
        return fwrite($this->connection, $msg, strlen($msg));
    }

    public function read(): string
    {
        return fread($this->connection, 1024) ?? '';
    }

}