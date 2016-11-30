<?php

namespace Tree6bee\Cf\Http;

use Tree6bee\Cf\Http\Request\Request as BasicRequest;
use Tree6bee\Cf\Http\Request\Uri;
use Tree6bee\Cf\Exceptions\Exception;
use Tree6bee\Cf\Contracts\Route as RouteContract;
use Tree6bee\Cf\Contracts\Application;
use Tree6bee\Cf\Contracts\Cookies;
use Tree6bee\Cf\Contracts\Session;

class Request extends BasicRequest
{
    /**
     * @var Uri
     */
    public $uri;

    /**
     * @var RouteContract
     */
    public $route;

    /**
     * Cookies ($_COOKIE).
     *
     * @var Cookies $cookies
     */
    public $cookies;

    /**
     * Session ($_SESSION).
     * 在获取到配置后中间件执行前设置
     *
     * @var Session $session
     */
    public $session;

    /**
     * @return Request
     */
    public static function capture()
    {
        //初始化 基础request
        return static::createFromBase(static::createFromGlobals());
    }

    //解析路由 获取 controller 和 model 等，方便在中间件中之间进行拦截
    public static function createFromBase(BasicRequest $request)
    {
        if (! $request instanceof static) {
            throw new Exception('Request对象必须为应用层对象');
        }

        //设置请求的基础uri
        $request->uri = new Uri($request);

        return $request;
    }

     public function setRoute(RouteContract $route)
     {
         $this->route = $route;
         $this->route->parseUri($this);
     }

    /**
     * cookies
     *
     * @return Request
     */
     public function setCookies(Cookies $cookies)
     {
         $this->cookies = $cookies;

         return $this;
     }

     /**
      * session
      *
      * @return Request
      */
     public function setSession(Session $session)
     {
         $this->session = $session;

         return $this;
     }
}
