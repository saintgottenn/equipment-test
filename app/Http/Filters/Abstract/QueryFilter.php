<?php

namespace App\Http\Filters\Abstract;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var Builder
     */
    protected Builder $builder;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function apply(Builder $builder): void
    {
        $this->builder = $builder;

        foreach ($this->request->query() as $method => $value) {
            if (method_exists($this, $method) && $value !== null) {
                call_user_func_array([$this, $method], (array)$value);
            }
        }

        $this->applyDefaultFilters();
    }

    /**
     * @return void
     */
    protected function applyDefaultFilters(): void
    {
        //
    }

}
