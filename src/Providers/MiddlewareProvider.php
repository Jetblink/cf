<?php

namespace Tree6bee\Cf\Providers;

use Tree6bee\Cf\Contracts\MiddlewareProvider as MiddlewareProviderContract;
use Tree6bee\Cf\Contracts\Application;

class MiddlewareProvider implements MiddlewareProviderContract
{
    protected $app;
 
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
 
    //-- Middleware --
    public function getMiddleware($middleware)
    {
        //不需要进行特殊处理的中间件
        $middleware = new $middleware();
        $middleware->app = $this->app;
        return $middleware;
    }
}
