<?php

namespace Tree6bee\Cf\Foundation;

use Tree6bee\Cf\Exceptions\Exception;
use Tree6bee\Cf\Contracts\Application as ApplicationContract;
use Tree6bee\Cf\Contracts\Config;
use Tree6bee\Cf\Contracts\ExceptionHandler;

/**
 * Application
 */
class Application implements ApplicationContract
{
    /**
     * 私有克隆函数，防止外办克隆对象
     */
    private function __clone()
    {
    }

    /**
     * 框架 Application 单例，静态变量保存全局实例
     */
    private static $instance = null;

    /**
     * 应用单例
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    protected function __construct()
    {
    }

    /**
     * 配置
     *
     * @var Config $config
     */
    protected $config;

    /**
     * 是否debug环境
     */
    protected $debug;

    /**
     * 异常处理接管类
     */
    protected $exceptionHandler;

    protected function init(Config $configObj, ExceptionHandler $exceptionHandler)
    {
        if (empty($this->config)) {
            $this->config = $configObj;
            $this->debug = $this->config->get('environment', 'production') == 'development' ? true : false;
            $this->exceptionHandler = $exceptionHandler;
        }
    }

    /**
     * @param  array  $bootstrappers
     */
    public function bootstrapWith($bootstrappers)
    {
        foreach ($bootstrappers as $bootstrapper) {
            (new $bootstrapper($this))->bootstrap();
        }
    }

    public function isDebug()
    {
        return $this->debug;
    }

    public function getExceptionHandler()
    {
        return $this->exceptionHandler;
    }

    public function config($item = null, $default = null, $file = 'main')
    {
        return $this->config->get($item, $default, $file);
    }
}
