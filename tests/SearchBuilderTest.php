<?php

namespace Nin\MysqlFtSearch\Tests;

use Mockery as m;
use Nin\MySqlFtSearch\SearchBuilder;

class SearchBuilderTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testFullTextWildcards()
    {
        $config = $this->app['config'];
        $searchBuilder = new SearchBuilder($config);

        $prefixWordOperator = $config->get('mysql-ft-search.prefix_word_operator');
        $suffixWordOperator = $config->get('mysql-ft-search.suffix_word_operator');

        $reflection = new \ReflectionClass(SearchBuilder::class);
        $method = $reflection->getMethod('fullTextWildcards');
        $method->setAccessible(true);
        $strCompare = $prefixWordOperator . 'foo' . $suffixWordOperator . ' '
            . $prefixWordOperator . 'bar' . $suffixWordOperator;

        $this->assertEquals($strCompare, $method->invokeArgs($searchBuilder, ['foo bar']));
    }

}
