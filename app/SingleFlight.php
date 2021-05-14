<?php


namespace App;


use Log;

class SingleFlight
{
    private $doing;

    public function do(string $key, callable $fn)
    {
        if ( isset($this->doing[$key])) {
            $call = $this->doing[$key];
            Log::info('waiting');
            $call->wait();
            return $call->getResult();
        }
        $call = new Call();
        $this->doing[$key] = $call;
        $call->do($fn);
        unset($this->doing[$key]);
        return $call->getResult();
    }
}