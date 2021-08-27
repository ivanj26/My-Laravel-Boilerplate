<?php

namespace App\Http\Modules;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModule
{
    /**
     * The class model.
     * 
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The query builder.
     * 
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $query;

    /**
     * Array of related models to eager load.
     * 
     * @var array
     */
    protected $eagers = [];

    /**
     * Set custom eagers load.
     * 
     * @param array|string relations
     * @return BaseModule this
     */
    public function with($relations)
    {
        if (is_string($relations)) {
            $relations = [$relations];
        }

        $this->eagers = $relations;
        return $this;
    }

    /**
     * Set order to query builder.
     * 
     * @param orderBy ordered by a given column.
     * @param sortBy ascending or descending order.
     * @return BaseModule this.
     */
    public function order($orderBy, $sortBy)
    {
        $this->query = $this->query
            ->orderBy($orderBy, $sortBy);

        return $this;
    }

    /**
	 * Create a new instance of the model's query builder
	 *
	 * @return $this
	 */
	protected function newQuery()
	{
		$this->query = $this->model->newQuery();
		return $this;
	}

    /**
	 * Add relationships to the query builder to eager load
	 *
	 * @return \Illuminate\Database\Eloquent\Builder $builder
	 */
	protected function eagerLoad()
	{ 
		return $this->query->with($this->eagers);
	}
 

    /**
     * Get all records in the database.
     * 
     * @param Closure modifier
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function findAll(Closure $modifier = null)
    {
        $builder = $this
            ->newQuery()
            ->eagerLoad();
        if (is_callable($modifier)) {
            $builder = $builder->where($modifier);
        }

        return $builder->get();
    }

    /**
     * Get paginated records in the database.
     * 
     * @param int limit
     * @param int page
     * @param array columns
     * @param Closure modifier
     * @param string pageName
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($limit = 10, $page = 1, Closure $modifier = null, array $columns = ['*'], $pageName = 'page')
    {
        $builder = $this
            ->newQuery()
            ->eagerLoad();
        if (is_callable($modifier)) {
            $builder = $builder->where($modifier);
        }

        return $builder->paginate($limit, $columns, $pageName, $page);
    }

    /**
     * Get first records from given modifier in the database.
     * 
     * @param Closure modifier
     * @param bool $throwError default true
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function findOne(Closure $modifier = null, bool $throwError = true)
    {
        $builder = $this
            ->newQuery()
            ->eagerLoad();

        if (is_callable($modifier)) {
            $builder = $builder->where($modifier);
        }

        if ($throwError) {
            return $builder->firstOrFail();
        }

        return $builder->first();
    }

    /**
     * Get records with given condition in the database.
     * 
     * @param String column
     * @param String value
     * @param Closure modifier
     * @param bool $throwError default true
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function findOneBy($column, $value, Closure $modifier = null, bool $throwError = true)
    {
        $builder = $this
            ->newQuery()
            ->eagerLoad()
            ->where($column, $value);

        if (is_callable($modifier)) {
            $builder = $builder->where($modifier);
        }

        if ($throwError) {
            return $builder->firstOrFail();
        }

        return $builder->first();
    }

    /**
     * Insert records to database.
     * 
     * @param array $payload
    * @return int $id
     */
    public function create($payload)
    {
        $builder = $this
            ->newQuery()
            ->eagerLoad();
        data_set($payload, 'created_at', Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'));
        data_set($payload, 'updated_at', Carbon::now()->isoFormat('YYYY-MM-DD HH:mm:ss'));
        return $builder->insertGetId($payload);
    }

    /**
     * Find and update record in database
     * 
     * @param string $column
     * @param string $value
     * @param array $payload
     * @return \Illuminate\Database\Eloquent\Model|object|static|null
     */
    public function findByAndUpdate(string $column, string $value, array $payload, Closure $modifier = null)
    {
        $model = $this->findOneBy($column, $value, $modifier, false);
        if (empty($model) || !($model instanceof Model)) {
            return false;
        }

        foreach ($payload as $key => $value) {
          $model[$key] = $value;
        }

        return $model->save();
    }
}