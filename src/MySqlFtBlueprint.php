<?php

namespace Nin\MySqlFtSearch;

use Illuminate\Database\Schema\Blueprint;

class MySqlFtBlueprint extends Blueprint
{
    /**
     * Specify an fulltext index for the table.
     *
     * @param string|array $columns
     * @param string|null $name
     * @param string|null $algorithm
     * @return \Illuminate\Support\Fluent
     */
    public function fulltext($columns, $name = null, $algorithm = null)
    {
        return $this->indexCommand('fulltext', $columns, $name, $algorithm);
    }
}
