<?php

namespace Tree6bee\Cf\Http\Request;

use Tree6bee\Cf\Http\Request\Bags\ParameterBag;
use Tree6bee\Cf\Http\Request\Bags\FileBag;
use Tree6bee\Cf\Http\Request\Bags\ServerBag;
use Tree6bee\Cf\Http\Request\Bags\HeaderBag;
use Exception;

/**
 * Request基础类
 */
class Request
{
    /**
     * Custom parameters.
     *
     * @var ParameterBag
     */
    public $attributes;

    /**
     * Query string parameters ($_GET).
     *
     * @var ParameterBag
     */
    public $query;

    /**
     * Request body parameters ($_POST).
     *
     * @var ParameterBag
     */
    public $request;

    /**
     * Uploaded files ($_FILES).
     *
     * @var FileBag
     */
    public $files;

    /**
     * Server and execution environment parameters ($_SERVER).
     *
     * @var ServerBag
     */
    public $server;

    /**
     * Headers (taken from the $_SERVER).
     *
     * @var HeaderBag
     */
    public $headers;

    /**
     * @var string
     */
    public $content;

    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array           $query      The GET parameters
     * @param array           $request    The POST parameters
     * @param array           $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array           $cookies    The COOKIE parameters
     * @param array           $files      The FILES parameters
     * @param array           $server     The SERVER parameters
     * @param string|resource $content    The raw body data
     */
    public function __construct($query = array(), $request = array(), $attributes = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
    {
        $this->query = new ParameterBag($query);
        $this->request = new ParameterBag($request);
        $this->attributes = new ParameterBag($attributes);
        // $this->cookies = new ParameterBag($cookies); //在获取到配置后中间件执行前设置,cookie同理
        $this->files = new FileBag($files); //@todo 继承后进行进一步处理
        $this->server = new ServerBag($server); //直接继承 ParameterBag
        $this->headers = new HeaderBag($this->server->getHeaders());    //独立实现
        $this->content = $content;
    }

    /**
     * Creates a new request with values from PHP's super globals.
     *
     * @return Request A new request
     */
    public static function createFromGlobals()
    {
        $attributes = array();

        // With the php's bug #66606, the php's built-in web server
        // stores the Content-Type and Content-Length header values in
        // HTTP_CONTENT_TYPE and HTTP_CONTENT_LENGTH fields.
        $server = $_SERVER;
        if ('cli-server' === PHP_SAPI) {
            if (array_key_exists('HTTP_CONTENT_LENGTH', $_SERVER)) {
                $server['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH'];
            }
            if (array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) {
                $server['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
            }
        }

        $content = null;

        $request = new static($_GET, $_POST, $attributes, $_COOKIE, $_FILES, $server, $content);

        //非POST请求下 request 需要用 content 来覆盖
        if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), array('PUT', 'DELETE', 'PATCH'))
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new ParameterBag($data);
        }

        return $request;
    }

    /**
     * Returns the request body content.
     *
     * @param bool $asResource If true, a resource will be returned
     *
     * @return string|resource The request body content or a resource to read the body stream
     *
     * @throws Exception
     */
    public function getContent($asResource = false)
    {
        $currentContentIsResource = is_resource($this->content);
        if (PHP_VERSION_ID < 50600 && false === $this->content) {
            //在 PHP 5.6 之前 php://input 打开的数据流只能读取一次； 数据流不支持 seek 操作。
            //参考文档 php://input 部分
            throw new Exception('getContent() can only be called once when using the resource return type and PHP below 5.6.');
        }

        if (true === $asResource) {
            if ($currentContentIsResource) {
                rewind($this->content);

                return $this->content;
            }

            // Content passed in parameter (test)
            if (is_string($this->content)) {
                $resource = fopen('php://temp', 'r+');
                fwrite($resource, $this->content);
                rewind($resource);

                return $resource;
            }

            $this->content = false;

            return fopen('php://input', 'rb');
        }

        if ($currentContentIsResource) {
            rewind($this->content);

            return stream_get_contents($this->content);
        }

        if (null === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }
}
