<?php

if (! function_exists('app')) {
    /**
     * 获取app
     *
     * @return \Tree6bee\Cf\Foundation\Application
     */
    function app()
    {
        return \Tree6bee\Cf\Foundation\Application::getInstance();
    }
}

if (! function_exists('config')) {
    /**
     * 获取config
     */
    function config($key = null, $default = null)
    {
        return app()->config($key, $default);
    }
}