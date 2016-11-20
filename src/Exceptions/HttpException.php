<?php

namespace Tree6bee\Cf\Exceptions;

/**
 * Cf框架基础异常
 */
class HttpException extends Exception
{
    private $httpStatusCode;

    /**
     * 异常处理基类
     *
     * ---以下为异常收集方法---
     * get_class($e) . ':[' .$e->getCode() . ']' . $e->getMessage()
     * '(' . $e->getFile() . ':' . $e->getLine() . ")\n";
     * $e->getTraceAsString()
     * other method: $e->getTrace() $e->__toString()
     * ---end---
     *
     * @param int $httpStatusCode http状态码
     * @param string $message 异常消息
     * @param int $code 错误码
     */
    public function __construct($httpStatusCode = 200, $message = '', $code = 0)
    {
        $this->httpStatusCode = $httpStatusCode;
        parent::__construct($message, $code);
    }
}