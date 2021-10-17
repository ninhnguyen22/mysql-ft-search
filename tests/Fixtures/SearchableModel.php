<?php

namespace Nin\MysqlFtSearch\Tests\Fixtures;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class SearchableModel extends Model
{
    use Searchable;

    protected $table = 'fts_tests';

    /**
     * The columns of the full text index
     */
    public $searchable = [
        'name',
        'job'
    ];
}
