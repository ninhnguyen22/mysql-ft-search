<?php

namespace Nin\MysqlFtSearch\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;

class SearchableModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SearchableModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'job' => $this->faker->jobTitle(),
        ];
    }

}
