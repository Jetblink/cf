<?php

namespace Tree6bee\Cf\Foundation\Http\Middleware;

use Closure;
use Tree6bee\Cf\Http\Request;
use Tree6bee\Cf\Http\Session;

class StartSession
{
    public function handle(Request $request, Closure $next)
    {
        //如果要切换为 response 对象，需要 setcookies
        return $next($this->startSession($request));
    }

    protected function startSession($request)
    {
        $sessionConf = $this->app->config('session');
        $session = new Session($sessionConf);
        $session->start();

        $request->setSession($session);

        return $request;
    }
}
