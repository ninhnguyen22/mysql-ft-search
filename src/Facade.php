<?php

namespace Nin\MySqlFtSearch;

use Nin\MySqlFtSearch\Schema\FtSchema;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return FtSchema::class;
    }
}
