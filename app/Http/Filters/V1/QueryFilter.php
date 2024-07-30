<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class QueryFilter
{
    protected Builder $builder;
    protected array $sortable = [];

    public function __construct(protected Request $request)
    {
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $builder;
    }

    protected function filter(array $filters)
    {
        foreach ($filters as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    protected function sort($value)
    {
        $sortables = explode(",", $value);

        foreach ($sortables as $sortable) {
            $direction = Str::startsWith($sortable, "-") ? "desc" : "asc";
            $column = Str::of($sortable)->remove("-")->snake()->value();

            if (in_array($column, $this->sortable)) {
                $this->builder->orderBy($column, $direction);
            }
        }
    }
}
