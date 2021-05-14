<?php


namespace App;

use Swoole\Coroutine\Channel;

class Engine
{
    /**
     * @var Channel
     */
    private $requestChan;
    private $workerNum;
    /**
     * @var Processor
     */
    private $processor;
    /**
     * @var Cache
     */
    private $cache;

    public function __construct($workerNum, Processor $processor, Cache $cache)
    {
        $this->requestChan = new Channel();
        $this->processor = $processor;
        $this->workerNum = $workerNum;
        $this->cache = $cache;
    }

    public function submit(string $request): void
    {
        $this->requestChan->push($request);
    }

    public function run(): void
    {
        $this->createWorker();

        while ($payload = $this->cache->popRequest()) {
            $this->requestChan->push($payload);
        }
    }

    public function createWorker(): void
    {
        for ($i = 0; $i < $this->workerNum; $i++) {
            go(function () {
                for (;;) {
                    $data = $this->requestChan->pop();
                    $this->processor->process($data[1]);
                }
            });
        }
    }

}