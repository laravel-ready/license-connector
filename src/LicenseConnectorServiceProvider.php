<?php

namespace LaravelReady\LicenseConnector;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

use LaravelReady\LicenseConnector\Services\ConnectorService;
use LaravelReady\LicenseConnector\Http\Middleware\LicenseGuardMiddleware;

final class LicenseConnectorServiceProvider extends ServiceProvider
{
    public function boot(Router $router): void
    {
        $this->bootPublishes();

        $this->loadMiddlewares($router);
    }

    public function register(): void
    {
        $this->registerConfigs();

        $this->app->singleton('license-connector', function () {
            return new ConnectorService();
        });
    }

    /**
     * Boot publishes
     */
    private function bootPublishes(): void
    {
        // configs
        $this->publishes([
            __DIR__ . '/../config/license-connector.php' => $this->app->configPath('license-connector.php'),
        ], 'license-connector-configs');
    }

    /**
     * Register package configs
     */
    private function registerConfigs(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/license-connector.php', 'license-connector');
    }

    /**
     * Load custom middlewares
     *
     * @param Router $router
     */
    private function loadMiddlewares(Router $router): void
    {
        $router->aliasMiddleware('license-connector', LicenseGuardMiddleware::class);
    }
}
