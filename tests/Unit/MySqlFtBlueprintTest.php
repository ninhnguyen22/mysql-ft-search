<?php

namespace Nin\MysqlFtSearch\Tests\Unit;

use Mockery as m;
use Nin\MySqlFtSearch\MySqlFtBlueprint;
use Illuminate\Database\Connection;
use Nin\MySqlFtSearch\MySqlFtGrammar;

class MySqlFtBlueprintTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testGenerateFulltextColumn()
    {
        $base = new MySqlFtBlueprint('posts', function ($table) {
            $table->fulltext('title');
        });

        $connection = m::mock(Connection::class);

        $blueprint = clone $base;

        $this->assertEquals([
            'alter table `posts` add FULLTEXT `posts_title_fulltext`(`title`)',
        ], $blueprint->toSql($connection, new MySqlFtGrammar()));
    }

}
