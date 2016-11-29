<?php

namespace Tree6bee\Cf\Http;

use Tree6bee\Support\Helpers\Encryption\Contracts\Encrypt;
use Tree6bee\Cf\Contracts\Cookies as CookiesContract;

/**
 * 框架cookie操作核心类
 * 使用cookie的建议：不建议将信息放cookie除非必须以减少传输
 * 类中没采用Arr数组操作类，所以 name 不能识别.表示的多维数组方式
 *
 * @todo 会变更到中间件中,所以需要对文件位置做对应调整
 */
class Cookies implements CookiesContract
{
    /**
     * @var Encrypt $encrypter
     */
    private $encrypter;

    /**
     * Cookie constructor.
     *
     * @param Encrypt $encrypt
     */
    public function __construct(Encrypt $encrypt)
    {
        $this->encrypter = $encrypt;
    }

    /**
     * 获取加密算法
     */
    public function getEncrypter()
    {
        return $this->encrypter;
    }

    /**
     * 获取cookie，然后解密
     *
     * @param null $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name = null, $default = null)
    {
        //返回所有的，不过是加密的并没有什么卵用
        if (empty($name)) {
            return $_COOKIE;
        }

        //返回具体的name的cookie
        if ($this->has($name)) {
            $cookie = $_COOKIE[$name];
            $cookie = $this->encrypter->decode($cookie);
            return unserialize($cookie);
        }
        return $default;
    }

    /**
     * 判断Cookie是否存在
     */
    public function has($name)
    {
        return isset($_COOKIE[$name]);
    }

    /**
     * 设置cookie
     * 加密cookie，然后设置
     *
     * @param $name
     * @param string $value
     * @param int $expire 有效期 时间戳 如果小于当前时间则表示删除 如果为0则直到浏览器关闭
     * @param string $path 生效目录 默认当前目录,/ 表示所有
     * @param string $domain 网站域名 如 example.com
     */
    public function set($name, $value = '', $expire = 0, $path = '/', $domain = '')
    {
        $value = serialize($value); //如果还有bug则外边加上base64转为16进制
        $value = $this->encrypter->encode($value);
        setcookie($name, $value, $expire, $path, $domain);
        $_COOKIE[$name] = $value;
    }

    /**
     * 删除某个Cookie值
     */
    public function del($name)
    {
        $this->set($name, null, time() - 3600);
    }
}
