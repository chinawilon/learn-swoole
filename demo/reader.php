<?php

include __DIR__.'/../vendor/autoload.php';

while ($data = fgets(STDIN, 1024)) {
    fwrite(STDOUT, $data);
}