<?php


namespace App;


use Swoole\Atomic;
use Swoole\Coroutine\Server\Connection;

class Protocol
{
    /**
     * @var Atomic
     */
    private $atomic;
    /**
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->atomic = new Atomic(time());
        $this->cache = $cache;
    }

    public function handle(Connection $connection): void
    {
        $reader = new BuffIO($connection);
        $writer = new BuffIO($connection);
        if (! $type = $reader->read(4) ) {
            return;
        }
        switch ($type) {
            case 'PUB ':
                $this->publish($writer, $reader);
                break;
            case 'SUB ':
                $this->subscribe($writer, $reader);
                break;
            default:
                $connection->send('404 forbidden');
                $connection->close();
        }
    }

    public function publish(BuffIO $writer, BuffIO $reader): void
    {
        for (;;) {
            if (! $len = $reader->read(4)) {
                return;
            }
            [, $length] = unpack('N', $len);
            if (! $payload = $reader->read($length)) {
                return;
            }
            $msg = $this->atomic->add();
            $writer->write(pack('Na*', strlen($msg), $msg));
            $writer->flush();

            go(function () use($payload) {
                $this->cache->pushRequest($payload);
            });
        }
    }

    public function subscribe(BuffIO $writer, BuffIO $reader)
    {

    }
}