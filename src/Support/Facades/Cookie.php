<?php

namespace Tree6bee\Cf\Support\Facades;

use Tree6bee\Cf\Http\Cookie as BasicCookie;
use Tree6bee\Support\Helpers\Encryption\Encrypt;

class Cookie extends Facade
{
    //因为是对象，所以facade不会保持单例
    private static $instance;

    protected static function getFacadeAccessor()
    {
        if (empty(self::$instance)) {
            $salt = config('cookie.salt', 'c!o*o^k#i-e_s%a$l@t');
            self::$instance = BasicCookie::getInstance(new Encrypt($salt));
        }

        return self::$instance;
    }
}
