<?php


namespace App;


use Swoole\Coroutine\Channel;

class Lock
{
    /**
     * @var Channel
     */
    private $chan;

    public function __construct()
    {
        $this->chan = new Channel(1);
    }

    public function lock(): void 
    {
        $this->chan->push(true);
    }

    public function unlock(): void 
    {
        $this->chan->pop();
    }
}