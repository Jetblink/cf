<?php

namespace Tree6bee\Cf\Exceptions;

use Tree6bee\Cf\Contracts\ExceptionHandler as ExceptionHandlerContract;
use Tree6bee\Support\Helpers\Exceptions\Handler as ExceptionHandler;
use Tree6bee\Cf\Support\Facades\Log;

class Handler extends ExceptionHandler implements ExceptionHandlerContract
{
    /** 
     * 错误日志记录
     */
    protected function report($e)
    {
        $content = $this->getLogOfException($e);
        Log::error($content);
    }
}
