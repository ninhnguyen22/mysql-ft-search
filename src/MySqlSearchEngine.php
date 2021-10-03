<?php

namespace Nin\MySqlFtSearch;

use Illuminate\Foundation\Application;
use Illuminate\Support\LazyCollection;
use Laravel\Scout\Builder;
use Laravel\Scout\Engines\Engine;

class MySqlSearchEngine extends Engine
{
    /**
     * @var SearchBuilder
     */
    protected $searchBuilder;

    /**
     * The Laravel application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Create a new engine instance.
     *
     * @param Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->setSearchBuilder($app['config']);
    }

    /**
     * Update the given model in the index.
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     * @return void
     */
    public function update($models)
    {
        //
    }

    /**
     * Remove the given model from the index.
     *
     * @param \Illuminate\Database\Eloquent\Collection $models
     * @return void
     */
    public function delete($models)
    {
        //
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $builder
     * @return array|mixed
     * @throws Exceptions\MissingSearchableException
     */
    public function search(Builder $builder)
    {
        $models = $this->performSearch($builder);
        return [
            'results' => $models->all(),
            'total' => count($models),
        ];
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $builder
     * @param int $perPage
     * @param int $page
     * @return array|mixed
     * @throws Exceptions\MissingSearchableException
     */
    public function paginate(Builder $builder, $perPage, $page)
    {
        $models = $this->performSearch($builder);

        return [
            'results' => $models->forPage($page, $perPage)->all(),
            'total' => count($models),
        ];
    }

    public function setSearchBuilder($config)
    {
        $this->searchBuilder = $this->getSearchBuilder($config);
    }

    public function getSearchBuilder($config)
    {
        return new SearchBuilder($config);
    }

    /**
     * Perform the given search on the engine.
     *
     * @param Builder $builder
     * @return \Illuminate\Support\Collection|mixed
     * @throws Exceptions\MissingSearchableException
     */
    protected function performSearch(Builder $builder)
    {
        $searchBuilder = $this->searchBuilder->init($builder);

        if ($builder->callback) {
            return call_user_func(
                $builder->callback,
                $searchBuilder,
                $builder->query
            );
        }

        return $searchBuilder->searchModels($builder);
    }

    /**
     * Pluck and return the primary keys of the given results.
     *
     * @param mixed $results
     * @return \Illuminate\Support\Collection
     */
    public function mapIds($results)
    {
        $results = $results['results'];

        return count($results) > 0
            ? collect($results)->pluck($results[0]->getKeyName())->values()
            : collect();
    }

    /**
     * Map the given results to instances of the given model.
     *
     * @param \Laravel\Scout\Builder $builder
     * @param mixed $results
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function map(Builder $builder, $results, $model)
    {
        $results = $results['results'];

        if (count($results) === 0) {
            return $model->newCollection();
        }

        $objectIds = collect($results)
            ->pluck($model->getKeyName())
            ->values()
            ->all();

        $objectIdPositions = array_flip($objectIds);

        return $model->getScoutModelsByIds(
            $builder, $objectIds
        )->filter(function ($model) use ($objectIds) {
            return in_array($model->getScoutKey(), $objectIds);
        })->sortBy(function ($model) use ($objectIdPositions) {
            return $objectIdPositions[$model->getScoutKey()];
        })->values();
    }

    /**
     * Map the given results to instances of the given model via a lazy collection.
     *
     * @param \Laravel\Scout\Builder $builder
     * @param mixed $results
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Support\LazyCollection
     */
    public function lazyMap(Builder $builder, $results, $model)
    {
        $results = $results['results'];

        if (count($results) === 0) {
            return LazyCollection::empty();
        }

        $objectIds = collect($results)
            ->pluck($model->getKeyName())
            ->values()->all();

        $objectIdPositions = array_flip($objectIds);

        return $model->queryScoutModelsByIds(
            $builder, $objectIds
        )->cursor()->filter(function ($model) use ($objectIds) {
            return in_array($model->getScoutKey(), $objectIds);
        })->sortBy(function ($model) use ($objectIdPositions) {
            return $objectIdPositions[$model->getScoutKey()];
        })->values();
    }

    /**
     * Get the total count from a raw result returned by the engine.
     *
     * @param mixed $results
     * @return int
     */
    public function getTotalCount($results)
    {
        return $results['total'];
    }

    /**
     * Flush all of the model's records from the engine.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function flush($model)
    {
        //
    }

    /**
     * Create a search index.
     *
     * @param string $name
     * @param array $options
     * @return mixed
     *
     * @throws \Exception
     */
    public function createIndex($name, array $options = [])
    {
        //
    }

    /**
     * Delete a search index.
     *
     * @param string $name
     * @return mixed
     */
    public function deleteIndex($name)
    {
        //
    }

}
