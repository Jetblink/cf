<?php

namespace Tree6bee\Cf\Contracts;

use Tree6bee\Cf\Http\Request;

/**
 * 路由协议
 * 设计到具体的路由解析
 */
interface Route
{
    public function __construct(Application $app);

    public function parseUri(Request $request);

    public function execute();
}