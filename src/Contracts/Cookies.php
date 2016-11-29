<?php

namespace Tree6bee\Cf\Contracts;

/**
 * cookies
 */
interface Cookies
{
    public function set($name, $value = '', $expire = 0, $path = '/', $domain = '');

    public function get($name = null, $default = null);

    public function has($name);

    public function del($name);
}
