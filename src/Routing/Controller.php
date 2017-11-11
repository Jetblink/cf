<?php

namespace Tree6bee\Cf\Routing;

use Tree6bee\Cf\Contracts\Application;
use Tree6bee\Support\Helpers\Arr;

/**
 * 框架基础控制器
 */
abstract class Controller
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    protected static $middleware = array();

    /**
     * @param string $action
     *
     * @return array
     */
    public static function getMiddleware($action)
    {
        return Arr::get(static::$middleware, $action, []);
    }
}
