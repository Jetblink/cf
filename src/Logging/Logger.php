<?php

namespace Tree6bee\Framework\Logging;

use Tree6bee\Support\Helpers\File;

class Logger
{
    /**
     * @var static
     */
    protected static $instance;

    //Facade写法
    public static function __callStatic($level, $args)
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        switch (count($args)) {
            case 1:
                return static::$instance->write($level, $args[0]);
            default:
                throw new \Exception('参数数量错误');
        }
    }

    public function __construct()
    {
    }

    public function write($level, $content)
    {
        $filename = $this->getLogFile($level);
        File::write($filename, $content);

        return $this;
    }

    protected function getLogFile($level)
    {
        $baseLogPath = $this->getStoragePath() . '/logs/';

        //可能权限出问题，做更多处理
        $wrapper = (PHP_SAPI == 'cli') ? 'cli' : 'web';

        $logPath = $baseLogPath . $wrapper;

        //日志路径
        $logPath = $this->getLogPath($logPath);

        return $logPath . $level . '.log';
    }

    protected function getLogPath($dir)
    {
        return $dir . '/'. date('Ym') . '/';
        //或则
        //return $dir . '/cf/';
    }

    protected function getStoragePath()
    {
//        return config('storage_path');
        return '/tmp';
    }
}
