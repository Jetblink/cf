<?php

namespace Tree6bee\Cf\Contracts;

/**
 * 框架配置获取
 */
interface Config
{
    public function get($item = null, $default = null, $file = 'main');
}
