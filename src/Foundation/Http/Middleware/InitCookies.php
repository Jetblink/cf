<?php

namespace Tree6bee\Cf\Foundation\Http\Middleware;

use Closure;
use Tree6bee\Cf\Http\Request;
use Tree6bee\Support\Helpers\Encryption\Encrypt;
use Tree6bee\Cf\Http\Cookies;

class InitCookies
{
    public function handle(Request $request, Closure $next)
    {
        //如果要切换为 response 对象，需要 setcookies
        return $next($this->setCookies($request));
    }

    protected function setCookies($request)
    {
        $salt = $this->app->config('cookie.salt', 'c!o*o^k#i-e_s%a$l@t');
        $request->setCookies(new Cookie(new Encrypt($salt)));

        return $request;
    }
}
