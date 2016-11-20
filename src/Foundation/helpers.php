<?php

use Tree6bee\Cf\Foundation\Application;

/**
 * 获取app
 */
if (! function_exists('app')) {
    function app()
    {
        return Application::getInstance();
    }
}

/**
 * 获取config
 */
if (! function_exists('config')) {
    function config($key = null, $default = null)
    {
        return app()->config($key, $default);
    }
}
//--end--

