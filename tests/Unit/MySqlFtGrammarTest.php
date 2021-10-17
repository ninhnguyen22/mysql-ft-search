<?php

namespace Nin\MysqlFtSearch\Tests\Unit;

use Mockery as m;
use Nin\MySqlFtSearch\MySqlFtBlueprint;
use Illuminate\Database\Connection;
use Nin\MySqlFtSearch\MySqlFtGrammar;

class MySqlFtGrammarTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testAddingFulltextKey()
    {
        $blueprint = new MySqlFtBlueprint('posts');
        $blueprint->fulltext('title');

        $connection = m::mock(Connection::class);
        $statements = $blueprint->toSql($connection, new MySqlFtGrammar());

        $this->assertCount(1, $statements);
        $this->assertSame('alter table `posts` add FULLTEXT `posts_title_fulltext`(`title`)',
            $statements[0]);
    }

}
