<?php

namespace Tree6bee\Cf\Foundation\Http\Middleware;

use Closure;
use Tree6bee\Cf\Http\Request;
use Tree6bee\Cf\Http\Session;

class StartSession
{
    public function handle(Request $request, Closure $next)
    {
        $sessionConf = $this->app->config('session');
        $this->session = new Session($sessionConf);
        $this->session->start();
    }
}
