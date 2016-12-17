<?php

namespace Tree6bee\Cf\Contracts;

/**
 * 框架配置获取
 */
interface Application
{
    public static function getInstance();

    public function bootstrapWith($bootstrappers);

    public function isDebug();

    public function getExceptionHandler();

    public function config($item = null, $default = null, $file = 'main');
}
