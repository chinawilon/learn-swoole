<?php

declare(ticks=1);

namespace Wilon;


use RuntimeException;

class Process
{
    /**
     * @var int
     */
    public $pid;
    /**
     * @var callable
     */
    private $fn;

    public function __construct(callable $fn)
    {
        $this->fn = $fn;
    }

    public static function signal(int $signo, $fn): bool
    {
        return pcntl_signal($signo, $fn);
    }

    public static function kill(int $pid, int $signo): bool
    {
        return posix_kill($pid, $signo);
    }

    public static function wait(&$status = null, $options = 0, $rusage = null): int
    {
        return pcntl_wait($status, $options, $rusage);
    }

    public static function waitpid($pid, &$status, $options = 0, $rusage = null): int
    {
        return pcntl_waitpid($pid, $status, $options, $rusage);
    }


    public function start(): void
    {
       $pid = pcntl_fork();
       if ( $pid < 0 ) {
           throw new RuntimeException('fork error');
       }

       // parent process
       if ( $pid > 0 ) {
           $this->pid = $pid;
           return;
       }

       // children process
        exit(
            call_user_func($this->fn)
        );
    }

}