<?php

namespace Tree6bee\Cf\Support\Facades;

use Tree6bee\Cf\Logging\Logger;
use Tree6bee\Cf\Logging\Writer;

class Log extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new Logger(new Writer(config('storage_path')));
    }
}
