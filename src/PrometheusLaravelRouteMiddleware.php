<?php

namespace Moneyplatform\LaravelPrometheusExporter;

use Closure;
use Illuminate\Http\Request as BaseRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Prometheus\Exception\MetricsRegistrationException;
use Prometheus\Histogram;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PrometheusLaravelRouteMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return Response
     * @throws MetricsRegistrationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $matchedRoute = $this->getMatchedRoute($request);

        $start = microtime(true);
        /** @var Response $response */
        $response = $next($request);
        $duration = microtime(true) - $start;
        /** @var PrometheusExporter $exporter */
        $exporter = app('prometheus');
        $histogram = $exporter->getOrRegisterHistogram(
            'response_time_seconds',
            'It observes response time.',
            [
                'method',
                'route',
                'status_code',
            ],
            config('prometheus.guzzle_buckets')
        );
        /** @var  Histogram $histogram */
        $histogram->observe(
            $duration,
            [
                $request->method(),
                $matchedRoute->uri(),
                $response->getStatusCode(),
            ]
        );
        return $response;
    }

    public function getMatchedRoute(Request|BaseRequest $request): Route
    {
        return RouteFacade::getRoutes()->match($request);
    }
}
