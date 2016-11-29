<?php

namespace Tree6bee\Cf\Support\Helpers;

use Exception;
use Tree6bee\Cf\Contracts\Session;
use Tree6bee\Support\Helpers\Str;

/**
 * Csrf
 */
class VerifyCsrfToken
{
    protected $csrfKey;

    protected $session;

    public function __construct($csrfKey, Session $session)
    {
        $this->csrfKey = $csrfKey;
        $this->session = $session;
    }

    /**
     * 获取csrf令牌
     */
    public function getToken()
    {
        return $this->session->get($this->csrfKey, '');
    }

    /**
     * 刷新csrf_token
     */
    public function refreshToken($replace = false)
    {
        if ($replace || empty($this->getToken())) { //更新
            $token = Str::rand(16);
            $this->session->set($this->csrfKey, $token);
        }

        return true;
    }

    /**
     * csrf验证
     * 必须为post提交，只能通过post获取表单数据
     * strtolower($_SERVER['REQUEST_METHOD'])
     */
    public function tokensMatch($val = '')
    {
        $token = $this->getToken();

        //token必须是设置状态同时必须验证通过
        if ($token && $val === $token) {
            return true;
        } else {
            throw new Exception('Csrf Forbidden.');
        }
    }
}
