<?php

namespace Moneyplatform\LaravelPrometheusExporter;

use Illuminate\Http\Request as BaseRequest;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Facades\Route as RouteFacade;
use Symfony\Component\HttpFoundation\Request;

class PrometheusLumenRouteMiddleware extends PrometheusLaravelRouteMiddleware
{
    public function getMatchedRoute(Request|BaseRequest $request): Route
    {
        $routeCollection = new RouteCollection();
        $routes = RouteFacade::getRoutes();

        foreach ($routes as $route) {
            $routeCollection->add(
                new Route(
                    $route['method'],
                    $route['uri'],
                    $route['action']
                )
            );
        }
        return $routeCollection->match($request);
    }
}
