<?php

namespace Tree6bee\Cf\Http\Request;

use Tree6bee\Cf\Http\Request\Bags\ParameterBag;

class Uri
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $uri = '';

    /**
     * 初始化
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;  //request: server && query

        //@todo 变更console模式到Kernel的方式组织
        if (php_sapi_name() == 'cli') { //命令行模式
            $this->setUri($this->getCliArgs());
        } else {    //http
            $this->fetchUri();
        }
    }

    /**
     * 获取uri
     */
    public function get()
    {
        return $this->uri;
    }

    protected function setUri($str = '')
    {
        //是否 $uri 只是一个 '/'
        $this->uri = ($str == '/') ? '' : $str;
    }

    /**
     * 解析命令行模式下的参数
     */
    protected function getCliArgs()
    {
        $args = array_slice($this->request->server->get('argv'), 1);
        return $args ? '/' . implode('/', $args) : '';
    }

    protected function fetchUri()
    {
        //通过 $_SERVER['REQUEST_URI']) 和 $_SERVER['SCRIPT_NAME'] 确定 $uri
        if ($uri = $this->detectUri()) {
            return $this->setUri($uri);
        }
        $this->setUri('');
    }

    /**
     * 尝试获取URI
     */
    protected function detectUri()
    {
        if (! $this->request->server->has('REQUEST_URI') || ! $this->request->server->has('SCRIPT_NAME')) {
            return '';
        }

        $uri = $this->request->server->get('REQUEST_URI');

        if (strpos($uri, $this->request->server->get('SCRIPT_NAME')) === 0) { //入口文件在根目录，url 可能为 /main.php
            $uri = substr($uri, strlen($this->request->server->get('SCRIPT_NAME')));
        } elseif (strpos($uri, dirname($this->request->server->get('SCRIPT_NAME'))) === 0) { //入口文件在子文件夹，url 可能为 /dir/main.php
            $uri = substr($uri, strlen(dirname($this->request->server->get('SCRIPT_NAME'))));
        }

        // This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
        // URI is found, and also fixes the QUERY_STRING server var and $_GET array.
        // if (strncmp($uri, '?/', 2) === 0) { //这里是为兼容 /main.php?/abc/xx/ 的形式 此处 $uri 为 ?/abc/xx/
        //     $uri = substr($uri, 2);
        // }

        //在这里 $uri 可能是 ( 这里有个奇怪的= ?xx=yy)的形式，也可能是直接xxx=xx，也可能是/
        $parts = preg_split('#\?#i', $uri, 2);
        $uri = $parts[0];

        //重置 $_SERVER['QUERY_STRING'] 和 $_GET
        $queryString = '';
        $get = array();
        if (isset($parts[1])) {
            $queryString = $parts[1];
            parse_str($queryString, $get);
        }
        $this->request->server->set('QUERY_STRING', $queryString);
        $_SERVER['QUERY_STRING'] = $queryString;
        $this->request->query = new ParameterBag($get);
        $_GET = $get;

        if ($uri == '/' || empty($uri)) {
            return '/';
        }

        //@todo 这里有bug，如果url 为www.domain.com///a/b/c/d这里返回 /b/c/d
        $uri = parse_url($uri, PHP_URL_PATH);

        // Do some final cleaning of the URI and return it
        return str_replace(array('//', '../'), '/', trim($uri, '/'));
    }
}
