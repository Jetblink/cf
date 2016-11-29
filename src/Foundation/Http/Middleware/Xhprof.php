<?php

namespace Tree6bee\Cf\Foundation\Http\Middleware;

use Closure;
use Tree6bee\Cf\Http\Request;
use Tree6bee\Cf\Support\Helpers\Xhprof as XhprofHelper;

/**
 * Class Xhprof
 *
 * @property \Tree6bee\Cf\Foundation\Application $app
 */
class Xhprof
{
    protected $xhprof;

    public function handle(Request $request, Closure $next)
    {
        // var_dump(array_slice(func_get_args(), 2));  //其余参数
        $xhprofOpen = $this->app->config('xhprof.open', false);
        if ($xhprofOpen) {
            $this->xhprof = new XhprofHelper($this->app->isDebug(), $this->app->config('xhprof.path'));
            $this->xhprof->begin();
        }

        $response = $next($request);

        if ($xhprofOpen) {
            $this->xhprof->finish();
        }

        //do something
        return $response;
    }
}
