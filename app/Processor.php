<?php


namespace App;


use Log;
use Throwable;
use function Swoole\Coroutine\Http\get;
use function Swoole\Coroutine\Http\post;

class Processor
{
    public function process(string $payload): void
    {
        $request = json_decode($payload, true);
        try {
            switch ($request['method']) {
                case 'GET':
                    if ( $http = get($request['uri']) ) {
                        Log::info($http->getBody());
                        $http->close();
                        return;
                    }
                    Log::error('get error', $http->errMsg);
                    break;
                case 'POST':
                    if ( $http = post($request['uri'], $request['body']) ) {
                        Log::info($http->getBody());
                        $http->close();
                        return;
                    }
                    Log::error('post error', $http->errMsg);
                    break;
                default:
                    Log::error($payload);
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }
    }

}