<?php


namespace App\Protocol;


use App\Connection\ConnectionInterface;
use Log;

class SegProtocol
{
    /**
     * @var string
     */
    private $left;

    /**
     * @var string
     */
    private $type;

    /**
     * @var resource
     */
    private $connection;

    /**
     * Handler constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function handle(string $data): void
    {
        $this->left .= $data;
        if (! $this->type ) {
            if ( strlen($this->left) < 4 ) {
                return;
            }
            $this->type = substr($this->left, 0, 4);
            $this->left = substr($this->left, 4);
        }

        if (strlen($this->left) >= 4) {
            $len = substr($this->left, 0, 4);
            [, $length] = unpack('N', $len);
            $total = 4 + $length;
            if ( strlen($this->left) >= $total) {
                $payload = substr($this->left, 4, $length);
//                Log::info($payload);
                $this->left = substr($this->left, $total);
                $send = random_int(10000, 99999);
//                fwrite($this->connection, pack('Na*', strlen($send), $send));
                $this->connection->write(pack('Na*', strlen($send), $send));
            }
        }
    }
}