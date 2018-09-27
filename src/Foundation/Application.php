<?php

namespace Tree6bee\Framework\Foundation;

use Tree6bee\Framework\Contracts\Application as ApplicationContract;
use Tree6bee\Framework\Contracts\MiddlewareProvider;
use Tree6bee\Framework\Pipeline\Pipeline;
use Tree6bee\Framework\Routing\Router;
use Tree6bee\Support\Helpers\Arr;
use Tree6bee\Support\Helpers\Exceptions\HandleExceptions;
use Tree6bee\Support\Helpers\Exceptions\Handler;

/**
 * Application
 */
class Application implements ApplicationContract, MiddlewareProvider
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
     *
     * @return static
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
     * 应用配置
     * -- environment (运行环境):
     *  - development(开发模式)
     *  - testing(单测模式暂时不考虑)
     *  - production(生产环境)
     *  - maintenance(维护模式)
     *
     * -- cfVersion (框架版本)
     * -- timezone (时区)
     *
     * - xhprof_dir util包路径
     *
     * @var array
     */
    protected $config = [
        //'environment'   => 'production',
        'debug'         => false,
        'cfVersion'     => 'CtxFramework/1.0',
        'timezone'      => 'PRC',
        // 'xhprof_dir'    => __DIR__ . '/../public/xhprof',
    ];

    /**
     * 获取应用配置
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function config($key = null, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * 运行
     */
    public function run()
    {
        $response = $this->handle();

        $response->send();
    }

    /**
     * 全局中间件
     */
    protected $middleware = [];

    /**
     * handle request
     *
     * @return Response
     */
    public function handle()
    {
        $this->bootstrap();

        $middleware = array_merge(
            $this->middleware,
            $this->router->getRouteMiddleware()
        );

        return $this->sendThroughPipeline($middleware, function () {
            return $this->router->execute($this);
        });
    }

    /**
     * Send the request through the pipeline with the given callback.
     *
     * @param  array  $middleware
     * @param  \Closure  $then
     * @return mixed
     */
    protected function sendThroughPipeline(array $middleware, \Closure $then)
    {
        if (count($middleware) > 0) {
            return (new Pipeline($this))
                ->send($this)
                ->through($middleware)
                ->then($then);
        }

        return $then();
    }

    public function getMiddleware($middleware)
    {
        //不需要进行特殊处理的中间件
        $middleware = new $middleware();
        //不需要了，已经在参数中进行了传递
        //$middleware->app = $this;
        return $middleware;
    }

    /**
     * @var Router
     */
    protected $router;

    /**
     * 启动初始化
     */
    protected function bootstrap()
    {
        $this->handleExceptions();
        $this->initRuntime();
        $this->initRouter();
    }

    /**
     * 异常接管
     */
    protected function handleExceptions()
    {
        (new HandleExceptions($this->setExceptionsHandler()))->handle();
    }

    /**
     * 构造异常接管对象
     * @return \Tree6bee\Support\Helpers\Exceptions\Contracts\ExceptionsHandler
     */
    protected function setExceptionsHandler()
    {
        return new Handler('', $this->config('cfVersion'));
    }

    protected function initRuntime()
    {
        mb_internal_encoding('UTF-8');

        header(sprintf("X-Powered-By: %s", $this->config('cfVersion')));

        // 设置中国时区
        date_default_timezone_set($this->config('timezone'));

        // $GLOBALS['uuidtolog'] = uniqid('', true);
    }

    /**
     * 初始化路由
     */
    protected function initRouter()
    {
        $this->router = new Router();
    }

    /**
     * 获取路由
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    public function getAttr($key = null, $default = null)
    {
        return $this->router->getAttr($key, $default);
    }
}
