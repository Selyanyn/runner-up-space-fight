<?php

namespace Hproject;

final class Logger
{
    public function __construct(
        private $logFileName,
    ) {

    }

    public function log(string $message)
    {
        $log = fopen($this->logFileName, 'a');
        fwrite($log, $message . PHP_EOL);
        fclose($log);
    }
}