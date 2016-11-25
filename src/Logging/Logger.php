<?php

namespace Tree6bee\Cf\Logging;

use Tree6bee\Cf\Contracts\LogWriter;
use Tree6bee\Cf\Logging\Writer;

class Logger
{
    /**
     * 日志写入类
     */
    private $writer;

    public function __construct(LogWriter $writer)
    {
        $this->writer = $writer;
    }

    public function info($message, $context = array())
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    public function debug($message, $context = array())
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    public function error($message, $context = array())
    {
        return $this->log(__FUNCTION__, $message, $context);
    }

    public function log($level, $message, $context)
    {
        $content = $message . "\n";

        if (! empty($context)) {
            $content .= print_r($context, true);
        }
        return $this->writer->write($level, $content);
    }

    public function getWriter()
    {
        return $this->writer;
    }
}
