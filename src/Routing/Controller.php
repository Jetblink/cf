<?php

namespace Tree6bee\Cf\Routing;

use Tree6bee\Cf\Contracts\Application;

/**
 * 框架基础控制器
 *
 * @todo 待完善
 */
abstract class Controller
{
    /**
     * @var Application
     */
    protected $app = '';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
