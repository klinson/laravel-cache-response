<?php

namespace Klinson\CacheResponse;

use Klinson\CacheResponse\Console\Command\Clear;
use Illuminate\Support\ServiceProvider;
use Klinson\CacheResponse\Middleware\CacheResponse as CacheResponseMiddleware;

class CacheResponseServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([realpath(__DIR__.'/../config/cacheresponse.php') => config_path('cacheresponse.php')]);

        $this->addMiddlewareAlias('cache_response', CacheResponseMiddleware::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Clear::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(realpath(__DIR__.'/../config/cacheresponse.php'), 'cacheresponse');

        $this->app->singleton('cache.response', function () {
            return new CacheResponse;
        });
    }

    /**
     * Register a short-hand name for a middleware. For compatibility
     * with Laravel < 5.4 check if aliasMiddleware exists since this
     * method has been renamed.
     *
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    protected function addMiddlewareAlias($name, $class)
    {
        $router = $this->app['router'];

        if (method_exists($router, 'aliasMiddleware')) {
            return $router->aliasMiddleware($name, $class);
        }

        return $router->middleware($name, $class);
    }
}
