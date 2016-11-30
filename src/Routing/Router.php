<?php

namespace Tree6bee\Cf\Routing;

use Tree6bee\Cf\Contracts\Application;
use Tree6bee\Cf\Http\Request;

class Router
{
    protected $app;

    /**
     * The request currently being dispatched.
     *
     * @var Request
     */
    protected $currentRequest;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * dispatch
     */
    public function dispatch(Request $request = null)
    {
        return $this->dispatchToRoute($request);
    }

    /**
     * get response
     *
     * @param Request $request
     */
    protected function dispatchToRoute(Request $request)
    {
        $route = $request->route;
        return $route->execute();
    }
}
