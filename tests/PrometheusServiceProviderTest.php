<?php

declare(strict_types = 1);

namespace Moneyplatform\LaravelPrometheusExporter\Tests;

use Moneyplatform\LaravelPrometheusExporter\PrometheusExporter;
use Moneyplatform\LaravelPrometheusExporter\PrometheusServiceProvider;
use Moneyplatform\LaravelPrometheusExporter\StorageAdapterFactory;
use Orchestra\Testbench\TestCase;
use Prometheus\Storage\Adapter;

/**
 * @covers \Moneyplatform\LaravelPrometheusExporter\PrometheusServiceProvider<extended>
 */
class PrometheusServiceProviderTest extends TestCase
{
    public function testServiceProvider() : void
    {
        $this->assertInstanceOf(Adapter::class, $this->app[Adapter::class]);
        $this->assertInstanceOf(PrometheusExporter::class, $this->app[PrometheusExporter::class]);
        $this->assertInstanceOf(StorageAdapterFactory::class, $this->app[StorageAdapterFactory::class]);

        $this->assertInstanceOf(Adapter::class, $this->app->get('prometheus.storage_adapter'));
        $this->assertInstanceOf(PrometheusExporter::class, $this->app->get('prometheus'));
        $this->assertInstanceOf(StorageAdapterFactory::class, $this->app->get('prometheus.storage_adapter_factory'));

        /* @var \Illuminate\Support\Facades\Route $router */
        $router = $this->app['router'];
        $this->assertNotEmpty($router->get('metrics'));

        /* @var \Illuminate\Support\Facades\Config $config  */
        $config = $this->app['config'];
        $this->assertTrue($config->get('prometheus.metrics_route_enabled'));
        $this->assertEmpty($config->get('prometheus.metrics_route_middleware'));
        $this->assertSame([], $config->get('prometheus.collectors'));
        $this->assertEquals('memory', $config->get('prometheus.storage_adapter'));
    }

    protected function getPackageProviders($app) : array
    {
        return [PrometheusServiceProvider::class];
    }
}
