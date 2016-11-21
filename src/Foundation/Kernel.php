<?php

namespace Tree6bee\Cf\Foundation;

use Tree6bee\Cf\Contracts\Application as ApplicationContract;
use Tree6bee\Cf\Http\Request;
use Tree6bee\Cf\Pipeline\Pipeline;
use Tree6bee\Cf\Contracts\Route as RouteContract;
use Tree6bee\Cf\Providers\MiddlewareProvider;
use Tree6bee\Cf\Routing\Route;
use Tree6bee\Cf\Routing\Router;

class Kernel
{
    /**
     * $bootstrappers 初始化启动项
     */
    protected $bootstrappers = array(
        '\Tree6bee\Cf\Foundation\Bootstrap\HandleExceptions',
        '\Tree6bee\Cf\Foundation\Bootstrap\Runtime',
    );

    /**
     * 全局中间件
     */
     protected $middleware = array();

    /**
     * 路由中间件
     */
     protected $routeMiddleware = array();

    /**
     * ApplicationContract
     */
    protected $app;

    /**
     * 路由协议
     * @var RouteContract $route
     */
    protected $route;

    public function __construct(ApplicationContract $app, RouteContract $route = null)
    {
        $this->app = $app;

        $this->route = $route ? $route : new Route($app);

        //第一时间加载启动项
        $this->bootstrap();
    }

    protected function bootstrap()
    {
        $this->app->bootstrapWith($this->bootstrappers);
    }

     public function handle(Request $request)
     {
         $request->setRoute($this->route);

         return $this->sendRequestThroughRouter($request);
     }

     protected function getMiddleware()
     {
         return $this->middleware;
     }

     protected function getMiddlewareProvider()
     {
         return new MiddlewareProvider($this->app);
     }

     /**
      * 路由中间件
      */
     protected function getRouteMiddleware(Request $request)
     {
         return $this->routeMiddleware;
     }

     protected function sendRequestThroughRouter(Request $request)
     {
         $middleware = array_merge($this->getMiddleware(), $this->getRouteMiddleware($request));

         return (new Pipeline($this->getMiddlewareProvider()))
             ->send($request)
             ->through($middleware)
             ->then($this->dispatchToRouter());
     }

     /**
      * Get the route dispatcher callback.
      *
      * @return \Closure
      */
     protected function dispatchToRouter()
     {
         $router = new Router($this->app);
         return function ($request = null) use ($router) {
             return $router->dispatch($request);
         };
     }

    // public function terminate(Request $request, $response)
    // {
    //     if (function_exists('fastcgi_finish_request')) {
    //         fastcgi_finish_request();
    //     } else {
    //         self::closeOutputBuffers(0, true);
    //     }
    //     //@todo 后台计算
    // }
    //
    // /**
    //  * 清除buffer
    //  *
    //  * @todo 移动到 Response 类中
    //  */
    // public static function closeOutputBuffers($targetLevel, $flush)
    // {
    //     $status = ob_get_status(true);
    //     $level = count($status);
    //     // PHP_OUTPUT_HANDLER_* are not defined on HHVM 3.3
    //     $flags = defined('PHP_OUTPUT_HANDLER_REMOVABLE') ? PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? PHP_OUTPUT_HANDLER_FLUSHABLE : PHP_OUTPUT_HANDLER_CLEANABLE) : -1;
    //
    //     while ($level-- > $targetLevel && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || $flags === ($s['flags'] & $flags) : $s['del'])) {
    //         if ($flush) {
    //             ob_end_flush();
    //         } else {
    //             ob_end_clean();
    //         }
    //     }
    // }
}
