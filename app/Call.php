<?php


namespace App;


use Swoole\Coroutine\Channel;

class Call
{
    private $result;
    /**
     * @var Channel
     */
    private $chan;

    public function __construct()
    {
        $this->chan = new Channel();
    }

    public function getResult()
    {
        return $this->result;
    }

    public function wait(): void 
    {
        $this->chan->push(true);
    }

    public function do(callable $fn): void
    {
        $this->chan->push(true);
        $this->result = $fn();
        $this->chan->close();
    }
}