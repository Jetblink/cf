<?php

namespace Tree6bee\Framework\Contracts;

interface MiddlewareProvider
{
    //-- Middleware --
    /**
     * 解析出中间件实例
     *
     * @param mixed $middleware 闭包 | 字符串: 类名:参数1,参数2 | 对象实例
     */
    public function getMiddleware($middleware);
}
