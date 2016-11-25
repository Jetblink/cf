<?php

namespace Tree6bee\Cf\Support\Facades;

use Tree6bee\Cf\Http\Session as BasicSession;
use Tree6bee\Support\Helpers\Encryption\Encrypt;

class Cookie extends Facade
{
    //因为是对象，所以facade不会保持单例
    private static $instance;

    protected static function getFacadeAccessor()
    {
        if (empty(self::$instance)) {
            $sessionConf = config('session');
            self::$instance = new BasicSession($sessionConf);
            if (config('session.auto')) {
                self::$instance->start();
            }
        }

        return self::$instance;
    }
}
