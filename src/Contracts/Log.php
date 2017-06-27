<?php

namespace Tree6bee\Cf\Contracts;

/**
 * 框架日志类
 */
interface Log
{
    public function write($level, $content);
}
