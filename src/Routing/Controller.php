<?php

namespace Tree6bee\Cf\Routing;

use Tree6bee\Cf\Http\Request;
use Tree6bee\Support\Helpers\Arr;

/**
 * 框架基础控制器
 *
 * @todo 待完善
 */
abstract class Controller
{
    protected static $middleware = array();

    /**
     * @var Request
     */
    protected $request = '';

    /**
     * @param string $action
     *
     * @return array
     */
    public static function getMiddleware($action)
    {
        return Arr::get(self::$middleware, $action, array());
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
