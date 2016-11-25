<?php

namespace Tree6bee\Cf\Logging;

use Tree6bee\Cf\Contracts\LogWriter;
use Tree6bee\Support\Helpers\Io;

class Writer implements LogWriter
{
    /**
     * 存储目录
     */
    protected $dir;

    /**
     * 日志内容
     */
    protected $log = array();

    public function __construct($dir)
    {
        $this->dir = $dir;

        //程序结束 第一个参数为回调，后续都是作为回调函数的参数
        register_shutdown_function(array($this, 'handleShutdown'));
    }

    /**
     * 异步写入
     */
    public function write($file, $content)
    {
        $this->log[$file] .= $content . "\n";

        return $this;
    }

    /**
     * 写入日志
     */
    public function flush()
    {
        foreach ($this->log as $file => $content) {
            $filename = $this->getLogFile($file);

            Io::write($filename, $content);
        }

        //清空所有的log
        $this->log = array();

        return $this;
    }

    protected function getLogFile($filename, $cutDate = true)
    {
        $logPath = $this->dir . '/logs/';

        $wrapper = (php_sapi_name() == 'cli') ? 'cli' : 'web';

        //日志路径
        if (true == $cutDate) {
            $logPath = $logPath . $wrapper . '/'. date('Ym') . '/';
        } else {
            $logPath = $logPath . $wrapper . '/def/';
        }

        return $logPath . $filename . '.log';
    }

    public function handleShutdown()
    {
        $this->flush();
    }
}
