<?php

namespace Nin\MysqlFtSearch\Tests\Unit;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Nin\MySqlFtSearch\ServiceProvider;

class TestCase extends OrchestraTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
