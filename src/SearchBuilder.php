<?php

namespace Nin\MySqlFtSearch;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Laravel\Scout\Builder;
use Nin\MySqlFtSearch\Exceptions\MissingSearchableException;

class SearchBuilder
{
    protected $columns;
    protected $term;
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Initialization
     *
     * @param Builder $builder
     * @return $this
     * @throws MissingSearchableException
     */
    public function init(Builder $builder)
    {
        $this->setSearchableColumns($builder);
        $this->term = $this->fullTextWildcards($builder->query);

        return $this;
    }

    /**
     * Get the Eloquent models for the given builder.
     *
     * @param Builder $builder
     * @return \Illuminate\Support\Collection
     */
    public function searchModels(Builder $builder)
    {
        $query = $builder->model->query()
            ->whereRaw("MATCH ({$this->columns}) AGAINST (? IN BOOLEAN MODE)", $this->term)
            ->when(count($builder->wheres) > 0, function ($query) use ($builder) {
                foreach ($builder->wheres as $key => $value) {
                    if ($key !== '__soft_deleted') {
                        $query->where($key, $value);
                    }
                }
            })
            ->when(count($builder->whereIns) > 0, function ($query) use ($builder) {
                foreach ($builder->whereIns as $key => $values) {
                    $query->whereIn($key, $values);
                }
            })
            ->orderBy($builder->model->getKeyName(), 'desc');

        return $this->ensureSoftDeletesAreHandled($builder, $query)
            ->get()
            ->values();
    }

    /**
     * Ensure that soft delete handling is properly applied to the query.
     *
     * @param \Laravel\Scout\Builder $builder
     * @param \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Query\Builder
     */
    protected function ensureSoftDeletesAreHandled($builder, $query)
    {
        if (Arr::get($builder->wheres, '__soft_deleted') === 0) {
            return $query->withoutTrashed();
        } elseif (Arr::get($builder->wheres, '__soft_deleted') === 1) {
            return $query->onlyTrashed();
        } elseif (in_array(SoftDeletes::class, class_uses_recursive(get_class($builder->model))) &&
            config('scout.soft_delete', false)) {
            return $query->withTrashed();
        }

        return $query;
    }

    /**
     * Set searchable columns.
     *
     * @param Builder $builder
     * @throws MissingSearchableException
     */
    protected function setSearchableColumns(Builder $builder)
    {
        if (!property_exists($builder->model, 'searchable')) {
            throw new MissingSearchableException("This model missing searchable properties.");
        }
        $this->columns = implode(',', $builder->model->searchable);
    }

    protected function fullTextWildcards($term)
    {
        // removing symbols used by MySQL
        $reservedSymbols = $this->config->get('mysql-ft-search.remove_symbols', []);
        $term = str_replace($reservedSymbols, '', $term);

        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            /*
             * applying + operator (required word) only big words
             * because smaller ones are not indexed by mysql
             */
            if (strlen($word) >= 2) {
                $words[$key] = $this->config->get('mysql-ft-search.prefix_word_operator', '*')
                    . $word . $this->config->get('mysql-ft-search.suffix_word_operator', '*');
            }
        }

        return implode(' ', $words);
    }

}
