<?php

namespace Tree6bee\Cf\Contracts;

/**
 * 框架配置获取
 */
interface LogWriter
{
    public function write($file, $content);

    public function flush();
}
