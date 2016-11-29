<?php

namespace Tree6bee\Cf\Foundation\Http\Middleware;

use Closure;
use Tree6bee\Cf\Http\Request;
use Tree6bee\Cf\Support\Helpers\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken
{
    public function handle(Request $request, Closure $next)
    {
        $this->verify($request);

        return $next($request);
    }

    public function verify($request)
    {
        $csrfKey = '_csrf'; //csrf的session和表单的name

        $verifier = new BaseVerifier($csrfKey, $request->session);
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        //csrf只会对post生效,session主动开启的时候才有效
        if ('get' == $method) { //['HEAD', 'GET', 'OPTIONS'] 读
            return $verifier->refreshToken();
        }

        $verifier->tokensMatch($request->request->get($csrfKey));
        // return $verifier->refreshToken(true);   //一次性token,如果匹配过的就更新
        return true;
    }
}
