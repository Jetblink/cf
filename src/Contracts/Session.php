<?php

namespace Tree6bee\Cf\Contracts;

/**
 * session
 */
interface Session
{
    public function has($name);

    public function get($name = null, $default = null);

    public function set($name, $value = null);

    public function clear();

    public function destroy();
}
