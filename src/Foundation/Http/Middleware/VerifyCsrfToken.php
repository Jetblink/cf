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
        $csrfKey = $this->app->config('csrf.key', '_csrf');

        $verifier = new BaseVerifier($csrfKey, $request->session);
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if ('get' == $method) { //['HEAD', 'GET', 'OPTIONS'] 读
            return $verifier->refreshToken();
        }

        if ($verifier->tokensMatch($request->request->get($csrfKey))) {
            // return $verifier->refreshToken(true);   //一次性token,如果匹配过的就更新
            return true;
        }
    }
}
