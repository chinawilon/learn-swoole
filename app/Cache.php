<?php


namespace App;


use Redis;

class Cache
{
    private $host;
    private $port;
    /**
     * @var Redis
     */
    private $redis;
    /**
     * @var Lock
     */
    private $lock;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->redis = new Redis();
        $this->lock = new Lock();
    }

    public function connect(): void
    {
        $this->redis->connect($this->host, $this->port);
        $this->redis->setOption(Redis::OPT_READ_TIMEOUT, -1);
    }


    public function pushRequest(string $payload): void
    {
        $this->lock->lock();
        $this->redis->rPush('request', $payload);
        $this->lock->unlock();
    }

    public function popRequest(): array
    {
        return $this->redis->blPop('request', 0);
    }

    public function pushResponse(string $payload): void
    {
        $this->redis->rPush('response', $payload);
    }

    public function popResponse(): array
    {
        return $this->redis->blPop('response', 0);
    }

}