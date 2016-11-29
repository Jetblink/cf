<?php

namespace Tree6bee\Cf\Http;

use Tree6bee\Support\Helpers\Arr;
use Tree6bee\Cf\Contracts\Session as SessionContract;

/**
 * 框架session核心类
 *
 * Session.name:这个就是SessionID储存的变量名称
 * php.ini : session.name = PHPSESSID //默认值
 *
 * Session.cookie_lifetime:这个代表SessionID在客户端Cookie储存的时间，
 * 默认值是“0”，代表浏览器一关闭，SessionID就作废
 *
 * Session.gc_maxlifetime:这个是Session数据在服务器端储存的时间
 *
 *  * @todo 会变更到中间件中,所以需要对文件位置做对应调整
 */
class Session implements SessionContract
{
    /**
     * session config
     */
    private $sessionConf;

    public function __construct($sessionConf)
    {
        $this->sessionConf = $sessionConf;
    }

    /**
     * 初始化session
     */
    public function start($id = null)
    {
        $sessionConf = $this->sessionConf;
        // $gcMaxLifetimeOld = ini_get('session.gc_maxlifetime');
        // if (! empty($sessionConf['gc_maxlifetime']) && $sessionConf['gc_maxlifetime'] >= 1) {
        //     ini_set('session.gc_maxlifetime', $sessionConf['gc_maxlifetime']);
        // }
        if (! empty($sessionConf['cookie_domain'])) {
            ini_set("session.cookie_domain", ".domain.com");
        }
        if (! empty($sessionConf['save_handler'])) {
            ini_set("session.save_handler", $sessionConf['save_handler']);
            ini_set("session.save_path", $sessionConf['save_path']);
        }
        // memc 方式存放session
        // ini_set("memcache.hash_strategy", "consistent");

        // 建议session不要放多台机器
        // 否则单独做一个授权的机器sso

        if (! empty($sessionConf['name'])) {
            session_name($sessionConf['name']);
        }
        if ($id) {
            //参考laravel vendor/laravel/framework/src/Illuminate/Session/Store.php
            //generateSessionId : return sha1(uniqid('', true).Str::random(25).microtime(true));
            //isValidId : return is_string($id) && preg_match('/^[a-f0-9]{40}$/', $id);
            session_id($id);
        }
        session_start();
    }

    /**
     * 判断是否存在
     */
    public function has($name)
    {
        return Arr::has($_SESSION, $name);
    }

    /**
     * 获取session
     */
    public function get($name = null, $default = null)
    {
        return Arr::get($_SESSION, $name, $default);
    }

    /**
     * 设置session
     */
    public function set($name, $value = null)
    {
        Arr::set($_SESSION, $name, $value);
    }

    /**
     * 清空Session
     * unset($_SESSION) 不可使用,它会将全局变量$_SESSION销毁
     */
    public function clear()
    {
        $_SESSION = array();
    }

    /**
     * 销毁Session
     */
    public function destroy()
    {
        //注销session,并不注销session变量,但把所有的session变量的值清空
        session_unset();
        //销毁整个Session 文件,注销所有的session变量,并且结束session会话
        session_destroy();
    }
}
