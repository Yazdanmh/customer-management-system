<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class QueryHelper
{
    public function paginateQuery(
        Builder $query,
        Request $request,
        array $searchableColumns = [],
        string $defaultSort = 'id',
        string $defaultDirection = 'asc'
    ): LengthAwarePaginator {
        if ($request->filled('search')) {
            $this->applySearchConditions($query, $request->search, $searchableColumns);
        }

        if ($request->filled('sort')) {
            $sort = $request->sort;
            $direction = $request->direction ?? 'asc';

            if (str_contains($sort, '.')) {
                [$relation, $column] = explode('.', $sort);
                $query->select($query->getModel()->getTable().'.*')
                      ->leftJoin($relation, "{$relation}.id", '=', "{$query->getModel()->getTable()}.{$relation}_id")
                      ->orderBy("{$relation}.{$column}", $direction);
            } else {
                $query->orderBy($sort, $direction);
            }
        } else {
            $query->orderBy($defaultSort, $defaultDirection);
        }

        return $query->paginate($request->perPage ?? 10)->withQueryString();
    }

    public function applySearchConditions(Builder $query, string $searchTerm, array $searchableColumns): void
    {
        $query->where(function ($subQuery) use ($searchTerm, $searchableColumns) {
            foreach ($searchableColumns as $field) {
                if (str_contains($field, '.')) {
                    [$relation, $col] = explode('.', $field);
                    $subQuery->orWhereHas($relation, fn($q) => $q->where($col, 'like', "%{$searchTerm}%"));
                } else {
                    $subQuery->orWhere($field, 'like', "%{$searchTerm}%");
                }
            }
        });
    }
}
