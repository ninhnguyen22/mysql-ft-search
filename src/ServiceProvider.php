<?php

namespace Nin\MySqlFtSearch;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Scout\EngineManager;
use Nin\MySqlFtSearch\Schema\FtSchema;
use Nin\MySqlFtSearch\Schema\FtSchemaBuilder;
use Illuminate\Foundation\Application;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/mysql-ft-search.php' => config_path('mysql-ft-search.php'),
        ]);

        $scoutDriverName = $this->app['config']->get('mysql-ft-search.scout_driver_name', 'mysql');
        resolve(EngineManager::class)->extend($scoutDriverName, function (Application $app) {
            return new MySqlSearchEngine($app);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/mysql-ft-search.php', 'mysql-ft-search'
        );

        $this->app->singleton(FtSchema::class, function (Application $app) {
            $ftSchemaBuilder = new FtSchemaBuilder($app);
            return $ftSchemaBuilder->getSchema();
        });
    }
}
