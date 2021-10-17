<?php

namespace Nin\MysqlFtSearch\Tests\Feature;

use Nin\MysqlFtSearch\Tests\Fixtures\SearchableModel;

class SearchBuilderTest extends TestCase
{

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

    public function test_it_can_retrieve_results_with_empty_search()
    {
        $models = SearchableModel::search()->get();
        $this->assertCount(1, $models);
    }

    public function test_it_can_retrieve_results()
    {
        $models = SearchableModel::search('Nghia')->get();
        $this->assertCount(1, $models);
        $this->assertEquals(1, $models[0]->id);

        $models = SearchableModel::search('Nghia')->where('job', 'developer')->get();
        $this->assertCount(1, $models);
        $this->assertEquals(1, $models[0]->id);

        $models = SearchableModel::search('Nghia')->where('job', 'doctor')->get();
        $this->assertCount(0, $models);

        $models = SearchableModel::search('developer')->get();
        $this->assertCount(2, $models);
        $this->assertEquals(1, $models[0]->id);
        $this->assertEquals(2, $models[1]->id);

        $models = SearchableModel::search('developer')->query(function ($query) {
            $query->where('name', 'like', 'Nguyen');
        })->get();
        $this->assertCount(1, $models);
        $this->assertEquals(2, $models[0]->id);

        $models = SearchableModel::search('Ninh Nghia')->get();
        $this->assertCount(2, $models);
        $this->assertEquals(1, $models[0]->id);
        $this->assertEquals(2, $models[1]->id);

        $models = SearchableModel::search('foo')->get();
        $this->assertCount(0, $models);

        $models = SearchableModel::search('deve')->get();
        $this->assertCount(2, $models);
        $this->assertEquals(1, $models[0]->id);
        $this->assertEquals(2, $models[1]->id);
    }

    public function test_it_can_paginate_results()
    {
        $models = SearchableModel::search('foo')->paginate();
        $this->assertCount(0, $models);

        $models = SearchableModel::search('developer')->paginate();
        $this->assertCount(2, $models);
    }
}
