<?php

namespace Nin\MySqlFtSearch\Schema;

use Illuminate\Foundation\Application;
use Nin\MySqlFtSearch\Exceptions\DriverNotSupportException;
use Nin\MySqlFtSearch\MySqlFtBlueprint as Blueprint;
use Nin\MySqlFtSearch\MySqlFtGrammar;

class FtSchemaBuilder
{
    protected $driverAccepts = [
        'mysql'
    ];

    /**
     * The Laravel application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get schema.
     *
     * @return \Illuminate\Database\Schema\Builder
     * @throws DriverNotSupportException
     */
    public function getSchema()
    {
        $connection = $this->getDbConnection();
        $schema = $connection->getSchemaBuilder();

        $schemaFtEnabled = $this->app['config']->get('mysql-ft-search.schema_ft_enabled', true);
        if ($schemaFtEnabled) {
            $this->checkDriver($connection->getDriverName());

            // Set Schema Grammar.
            // Add method to compile a fulltext index command.
            $connection->setSchemaGrammar(new MySqlFtGrammar());

            $schema = $connection->getSchemaBuilder();

            // Set the Schema Blueprint resolver callback.
            // Add method to create fulltext index.
            $schema->blueprintResolver(function ($table, $callback) {
                return new Blueprint($table, $callback);
            });
        }

        return $schema;
    }

    /**
     * Check driver support
     *
     * @param string $driverName
     * @throws DriverNotSupportException
     */
    protected function checkDriver($driverName)
    {
        if (!in_array($driverName, $this->driverAccepts)) {
            throw new DriverNotSupportException();
        }
    }

    /**
     * Get db from application dependencies.
     *
     * @return \Illuminate\Database\DatabaseManager
     */
    protected function getDb()
    {
        return $this->app->get('db');
    }

    /**
     * Get db connection.
     *
     * @return \Illuminate\Database\Connection
     */
    protected function getDbConnection()
    {
        return $this->getDb()->connection();
    }

}
