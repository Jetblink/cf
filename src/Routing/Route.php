<?php

namespace Tree6bee\Cf\Routing;

use Tree6bee\Cf\Contracts\Application;
use Tree6bee\Cf\Http\Request;
use Tree6bee\Cf\Http\Request\Bags\ParameterBag;
use Tree6bee\Cf\Exceptions\HttpException;
use Tree6bee\Cf\Contracts\Route as RouteContract;

class Route implements RouteContract
{
    /**
     * @var Request
     */
    protected $request = '';

    /**
     * @var string
     * 这里的uri跟Uri类的不一定一样，因为后缀会去掉后缀重新赋值给该类
     */
    protected $uri = '';

    /**
     * @var Application
     */
    protected $app = '';

    /**
     * 应用模块
     */
    protected $module = '';
 
    /**
     * 应用控制器
     */
    protected $controller = '';
 
    /**
     * 应用的方法
     */
    protected $action = '';

    public function __construct(Application $app)
    {
        $this->app = $app;  //用来获取配置
    }

    /**
     * 运行控制器
     */
    public function execute()
    {
        // echo $this->module, "#", $this->controller, "#", $this->action;exit;
        $controller = $this->getController();
        $app = new $controller($this->request);
        $action = $this->action;
        if (! is_callable(array($app, $action))) {
            throw new HttpException(404, '方法:' . $action . '@' . $controller . '不可调用.');
        }

        //beforeAction 和 afterAction 采用中间件实现
        return $app->$action();
    }

    /**
     * @see \Tree6bee\Cf\Routing\Controller
     */
    public function getController()
    {
        $namespace = '\\' . $this->app->config('namespace', 'App') . '\\Controllers\\';

        $controller = $namespace . (empty($this->module) ? '' : ucfirst($this->module) . '\\') . ucfirst($this->controller);

        if (! class_exists($controller)) {
            throw new HttpException(404, '控制器:' . $controller . '不存在.');
        }

        return $controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    /**
     * 进一步处理uri参数
     *
     * @param Request $request
     * @return bool|mixed
     */
    public function parseUri(Request $request)
    {
        $this->request = $request;
        $this->uri = $request->uri->get();

        if ($this->uri == '') {
            return $this->setRoute($this->app->config('dispatch.default_module'));
        }

        // Do we need to remove the URL suffix?
        $this->removeUrlSuffix();

        // 解析剩余的uri 同时返回路由必需参数
        return $this->explodeSegments();
    }

    /**
     * 设置路由
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return true
     */
    public function setRoute($module, $controller = '', $action = '')
    {
        $this->module = $module;
        $this->controller = $controller ? $controller : $this->app->config('dispatch.default_controller', 'index');
        $this->action = $action ? $action : $this->app->config('dispatch.default_action', 'index');

        return true;
    }

    /**
     * 去掉伪静态后缀
     */
    protected function removeUrlSuffix()
    {
        $url_suffix_config = $this->app->config('dispatch.url_suffix', '');
        if (! empty($url_suffix_config)) {
            $part =  pathinfo($this->uri);
            $url_suffix = isset($part['extension']) ? $part['extension'] : '';
            if (empty($url_suffix) || $url_suffix !== $url_suffix_config) {  //不做灵活处理方便统一url
                throw new HttpException(404, 'deny');
            }
            $this->uri = preg_replace("|".preg_quote('.' . $url_suffix_config)."$|i", "", $this->uri);
        }
    }

    /**
     * 拆分参数
     */
    protected function explodeSegments()
    {
        $path_separator = $this->app->config('dispatch.path_separator', '/');
        $url_var = $this->app->config('dispatch.url_var', '_URI_');
        $paths = explode($path_separator, trim($this->uri, '/'));    //因为是用 '/' 开头的
        $args = array();    //用于uri中参数获取
        if ($url_var) {
            // 直接通过$_GET['_URL_'][2] $_GET['_URL_'][3] 获取URL参数 方便不用路由时参数获取
            $args[$url_var] = $paths;
        }

        //获取出路由需要的对应值

        //如果要支持3层结构的url，必须提供默认的module
        $sliceNum = empty($this->app->config('dispatch.default_module')) ? 2 : 3;
        $router_arr = array_splice($paths, 0, $sliceNum);
        if (2 == $sliceNum) {
            array_unshift($router_arr, null);
        }

        // 解析剩余的URL参数
        if ($paths) {
            // preg_replace e 参数 有Deprecated(不建议) 提示
            // preg_replace('@(\w+)\/([^\/]+)@e', '$var[\'\\1\']=strip_tags(\'\\2\');', implode('/',$paths));
            // 闭包函数需要 php 版本大于 5.3
            // $var = array();
            preg_replace_callback('@(\w+)\/([^\/]+)@', function ($matches) use (&$args) {
                // $var[$matches[1]] = $matches[2];
                $args[$matches[1]] = $matches[2];
                return '';
            }, implode('/', $paths));
        }

        //设置路由控制器
        $this->request->attributes = new ParameterBag($args);
        return call_user_func_array(array($this, 'setRoute'), $router_arr);
    }
}
