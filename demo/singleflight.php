<?php


use App\SingleFlight;
use function Co\run;

include __DIR__.'/../vendor/autoload.php';

run(static function () {
    $s = new SingleFlight();
    for ($i = 0; $i < 10; $i++) {
        go(static function () use($s, $i) {
            Log::info($i);
            Log::info($i, '=>', $s->do('test', function () use($i) {
                Log::info($i);
                sleep(1);
                return $i;
            }));
        });
    }
});