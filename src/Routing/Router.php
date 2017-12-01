<?php

namespace Tree6bee\Cf\Routing;

use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use InvalidArgumentException;
use RuntimeException;
use Tree6bee\Cf\Contracts\Application;
use Tree6bee\Cf\Exceptions\HttpException;
use Tree6bee\Cf\Foundation\Response;
use Tree6bee\Support\Helpers\Arr;

class Router
{
    /**
     * Path to fast route cache file. Set to false to disable route caching
     *
     * @var string|False
     */
    protected $cacheFile = false;

    /**
     * @var string 默认 handler 名
     */
    protected $defaultHandler = 'defaultHandler';

    /**
     * Router constructor.
     * @param string|boolean $cacheFile 缓存文件
     */
    public function __construct($cacheFile = false)
    {
        if (!is_string($cacheFile) && $cacheFile !== false) {
            throw new InvalidArgumentException('Router cacheFile must be a string or false');
        }

        if ($cacheFile !== false && !is_writable(dirname($cacheFile))) {
            throw new RuntimeException('Router cacheFile directory must be writable');
        }

        $this->cacheFile = $cacheFile;

        $this->dispatch();
    }

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return $this
     */
    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return $this
     */
    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return $this
     */
    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return $this
     */
    public function patch($uri, $action)
    {
        $this->addRoute('PATCH', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return $this
     */
    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return $this
     */
    public function options($uri, $action)
    {
        $this->addRoute('OPTIONS', $uri, $action);

        return $this;
    }

    /**
     * Register a route with the application.
     *
     * @param  string  $uri
     * @param  mixed  $action
     * @return $this
     */
    public function any($uri, $action)
    {
        $this->addRoute(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $uri, $action);

        return $this;
    }

    /**
     * @var RouteCollector
     */
    protected $routeCollector;

    /**
     * @return Dispatcher
     */
    protected function createDispatcher()
    {
        if ($this->dispatcher) {
            return $this->dispatcher;
        }

        $routeDefinitionCallback = function (RouteCollector $r) {
            $this->routeCollector = $r;
            $this->getRouteDefinition();
        };

        if ($this->cacheFile) {
            $this->dispatcher = \FastRoute\cachedDispatcher($routeDefinitionCallback, [
                'cacheFile' => $this->cacheFile,
            ]);
        } else {
            $this->dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);
        }

        return $this->dispatcher;
    }

    /**
     * 获取路由定义
     */
    protected function getRouteDefinition()
    {
        //默认路由 映射方式
        $this->any('[{module}[/{controller}[/{action}[/{paths:.+}]]]]', $this->defaultHandler);
    }

    /**
     * Add a route to the collection.
     *
     * @param  array|string  $method
     * @param  string  $uri
     * @param  mixed  $action
     * @return void
     * @throws  \Exception
     */
    protected function addRoute($method, $uri, $action)
    {
        $uri = '/'.trim($uri, '/');
        if (is_string($action)) {
            $action = ['uses' => $action];
        }

        if (! is_array($action) || empty($action['uses']) || ! is_string($action['uses'])) {
            throw new \Exception('错误的路由' . $uri);
        }

        $this->routeCollector->addRoute($method, $uri, $action);
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function setDispatcher(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return string
     */
    public function getHttpMethod()
    {
        return Arr::get($_SERVER, 'REQUEST_METHOD', 'GET');
    }

    /**
     * 参考 slim 实现
     * @return bool|string
     */
    public function getUri()
    {
        if (PHP_SAPI == 'cli') {
            return '/'. ltrim(Arr::get($_SERVER['argv'], 1, '/'), '/');
        }

        $requestUri = parse_url('http://example.com' . Arr::get($_SERVER, 'REQUEST_URI', ''), PHP_URL_PATH);

        $requestUri = empty($requestUri) ? '/' : $this->filterPath($requestUri);

        return '/' . ltrim($requestUri, '/');

        // nginx 这种配置不可能走下边逻辑 try_files $uri $uri/ /index.php?$query_string;

        // Path
        // $requestScriptName = parse_url(Arr::get($_SERVER, 'SCRIPT_NAME', ''), PHP_URL_PATH);
        // $requestScriptDir = dirname($requestScriptName);

        // parse_url() requires a full URL. As we don't extract the domain name or scheme,
        // we use a stand-in.
        // $requestUri = parse_url('http://example.com' . Arr::get($_SERVER, 'REQUEST_URI', ''), PHP_URL_PATH);
        //
        // $basePath = '';
        // $virtualPath = $requestUri;
        // if (stripos($requestUri, $requestScriptName) === 0) {
        //     $basePath = $requestScriptName;
        // } elseif ($requestScriptDir !== '/' && stripos($requestUri, $requestScriptDir) === 0) {
        //     $basePath = $requestScriptDir;
        // }
        //
        // if ($basePath) {
        //     $virtualPath = ltrim(substr($requestUri, strlen($basePath)), '/');
        // }
        //
        // $requestUri = empty($virtualPath) ? '/' : $this->filterPath($virtualPath);
        //
        // return '/' . ltrim($requestUri, '/');
    }

    /**
     * Filter Uri path.
     *
     * This method percent-encodes all reserved
     * characters in the provided path string. This method
     * will NOT double-encode characters that are already
     * percent-encoded.
     *
     * @param  string $path The raw uri path.
     * @return string       The RFC 3986 percent-encoded uri path.
     * @link   http://www.faqs.org/rfcs/rfc3986.html
     */
    protected function filterPath($path)
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-\.~:@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $path
        );
    }

    /**
     * @var string $controller
     */
    protected $controller;

    /**
     * @var string $action
     */
    protected $action;

    /**
     * @var array $attr
     */
    protected $attr;

    /**
     * Dispatch the incoming request.
     *
     * @throws HttpException
     */
    protected function dispatch()
    {
        $routeInfo = $this->createDispatcher()->dispatch($this->getHttpMethod(), $this->getUri());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new HttpException(404, 'NOT FOUND');
            case Dispatcher::METHOD_NOT_ALLOWED:
                // $allowedMethods = $routeInfo[1]; //允许的方法
                throw new HttpException(405, 'Method Not Allowed');
            case Dispatcher::FOUND:
                // $routeInfo = [Dispatcher::FOUND, 'handler', 'vars']
                break;
            default: //不会出现这种情况，这里运行不到
                throw new HttpException(404, 'NOT FOUND');
        }

        $this->parseRouteInfo($routeInfo[1], $routeInfo[2]);
    }

    /**
     * 路由默认 module controller action
     */
    protected $defRouteVar = [
        'module'        => 'home',
        'controller'    => 'index',
        'action'        => 'index',
    ];

    /**
     * 解析映射路由和路由文件中的路由
     *
     * @param $handler
     * @param $var
     * @throws HttpException
     */
    protected function parseRouteInfo($handler, $var)
    {
        if ($this->defaultHandler !== $handler['uses']) {
            $this->parseDefinitionRouteInfo($handler, $var);
            return ;
        }

        $var = array_merge($this->defRouteVar, $var);
        $args = [];
        // 解析剩余的URL参数
        if (isset($var['paths'])) {
            // preg_replace e 参数 有Deprecated(不建议) 提示
            // preg_replace('@(\w+)\/([^\/]+)@e', '$var[\'\\1\']=strip_tags(\'\\2\');', implode('/',$paths));
            // 闭包函数需要 php 版本大于 5.3
            // $var = array();
            preg_replace_callback('@(\w+)\/([^\/]+)@', function ($matches) use (&$args) {
                $args[$matches[1]] = $matches[2];
            }, $var['paths']);
        }

        $this->controller = $this->getControllerName(sprintf(
            '%s\\%s',
            ucfirst($var['module']),
            ucfirst($var['controller'])
        ));

        $this->action = $var['action'];

        $this->attr = $args;
    }

    /**
     * @param string $controller
     * @return string
     * @throws HttpException
     */
    protected function getControllerName($controller)
    {
         if (PHP_SAPI == 'cli') { //命令行模式
             $controller = '\\App\\Commands\\' . $controller;
         } else {
             $controller = '\\App\\Controllers\\' . $controller;
         }

        if (! class_exists($controller)) {
            throw new HttpException(404, '控制器:' . $controller . '不存在.');
        }

        return $controller;
    }

    /**
     * 解析路由文件中的普通路由
     *
     * @param $handler
     * @param $var
     * @throws HttpException
     */
    protected function parseDefinitionRouteInfo($handler, $var)
    {
        list($controller, $action) = array_pad(explode('@', $handler['uses'], 2), 2, '');

        $this->controller = $this->getControllerName($controller);

        if (empty($action)) {
            throw new HttpException(404, '错误的路由');
        }

        $this->action = $action;

        $this->attr = $var;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getAttr($key = null, $default = null)
    {
        return Arr::get($this->attr, $key, $default);
    }

    /**
     *
     * 路由中间件
     *
     * @return array
     */
    public function getRouteMiddleware()
    {
        /** @var \Tree6bee\Cf\Routing\Controller $controller */
        $controller = $this->controller;
        $controllerMiddleware = $controller::getMiddleware($this->action);

        return (array)$controllerMiddleware;
    }

    public function execute(Application $app)
    {
        /** @var \Tree6bee\Cf\Routing\Controller $controller */
        $controller = $this->controller;
        $response = (new $controller($app))->{$this->action}();

        return new Response($response);
    }
}
