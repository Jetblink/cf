<?php

namespace Tree6bee\Cf\Foundation\Bootstrap;

use Tree6bee\Cf\Contracts\Application;
use Tree6bee\Support\Helpers\Exceptions\HandleExceptions as BasicHandleExceptions;

/**
 * 框架系统异常错误处理接管类
 * 参考 Laravel vendor/laravel/framework/src/Illuminate/Foundation/Bootstrap/HandleExceptions.php
 *
 * @copyright sh7ning 2016.1
 * @author    sh7ning
 * @version   0.0.1
 */
class HandleExceptions
{
    /**
     * @var $app
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bootstrap()
    {
        $handler = $this->app->getExceptionHandler();
        new BasicHandleExceptions(false, function ($e) use ($handler) {
            call_user_func([$handler, 'handle'], $e);
        });
    }
}
