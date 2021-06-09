<?php


namespace App\Connection;


interface ConnectionInterface
{
    public function write(string $msg);
    public function read(): string ;
    public function close();
}