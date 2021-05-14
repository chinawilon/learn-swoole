<?php

use App\Engine;
use function Co\run;

include __DIR__.'/../vendor/autoload.php';

run(static function () {
    $engine = new Engine(100);
    $engine->run();
    for (;;) {
        $engine->submit(1);
    }
});