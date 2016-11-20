<?php

namespace Tree6bee\Cf\Foundation\Bootstrap;

use Tree6bee\Cf\Contracts\Application;

/**
 * 设置运行时环境
 */
class Runtime
{
    /**
     * @var $app
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function bootstrap()
    {
        // 设置中国时区
        date_default_timezone_set($this->app->config('timezone', 'PRC'));

        mb_internal_encoding('UTF-8');

        header("X-Powered-By:CtxFramework");

        // $GLOBALS['uuidtolog'] = uniqid('', true);   //@todo
        // Log::$storagePath = $this->app->config->get('storage_path'); //@todo 初始化
        $this->definedPhpVersion();
    }

    protected function definedPhpVersion()
    {
        // $version_id = $major_version * 10000 + $minor_version * 100 + $release_version
        if (! defined('PHP_VERSION_ID')) {
            // This constant was introduced in PHP 5.2.7
            $version = explode('.', PHP_VERSION);
            define(
                'PHP_VERSION_ID',
                $version[0] * 10000
                + $version[1] * 100
                + $version[2]
            );
        }
    }
}
