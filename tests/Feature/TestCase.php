<?php

namespace Nin\MysqlFtSearch\Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Scout\EngineManager;
use Nin\MySqlFtSearch\MySqlSearchEngine;
use Nin\MysqlFtSearch\Tests\Fixtures\SearchableModelFactory;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Nin\MySqlFtSearch\ServiceProvider;

class TestCase extends BaseTestCase
{
    use WithFaker;

    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function defineEnvironment($app)
    {
        $app->make('config')->set('scout.driver', 'fake');
    }

    protected function defineDatabaseMigrations()
    {
        $this->setUpFaker();
        $this->loadLaravelMigrations();
         $this->loadMigrationsFrom('tests/Migrations');

        SearchableModelFactory::new()->create([
            'name' => 'Ninh Nghia',
            'job' => 'developer',
        ]);

        SearchableModelFactory::new()->create([
            'name' => 'Ninh Nguyen',
            'job' => 'developer',
        ]);

        SearchableModelFactory::new()->create([
            'name' => 'Tom Hiddleston',
            'job' => 'doctor',
        ]);
    }

}
