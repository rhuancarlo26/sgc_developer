<?php

namespace App\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

trait Searchable
{
    /**
     * Search Entries based on the provided params with a "%like%" operation
     * It can also search relations columns using "whereHas" when the searchColumn is separated by dots
     *
     * @param string|null $searchColumn
     * @param mixed $searchValue
     * @return Builder
     */
    public function search(?string $searchColumn, ?string $searchValue): Builder
    {
        if ($searchColumn && str_contains($searchColumn, '.')) {

            $explodedSearchColumn = explode('.', $searchColumn);

            $column = last($explodedSearchColumn);
            $relation = implode('.', array_slice($explodedSearchColumn, 0, -1));

            return $this->model->query()->whereHas(
                $relation,
                fn ($query) => $query->where($column, 'like', "%$searchValue%")
            );
        }

        return $this->model::query()->when(
            $searchColumn,
            fn ($query, $column) => $query->where($column, 'like', "%$searchValue%")
        );
    }

    public function searchAllColumns(?string $columns, ?string $value): Builder
    {
        $queryModel = $this->model->query();

        if ($value) {
            foreach (explode(",", $columns) as $column) {
                if ($column && str_contains($column, '.')) {

                    $explodedColumn = explode('.', $column);

                    $column = last($explodedColumn);
                    $relation = implode('.', array_slice($explodedColumn, 0, -1));

                    $queryModel->orWhereHas(
                        $relation,
                        fn ($query) => $query->Where($column, 'like', "%$value%")
                    );
                } else {
                    $queryModel->when(
                        $column,
                        fn ($query, $column) => $query->orWhere($column, 'like', "%$value%")
                    );
                }
            }
        }

        return $queryModel;
    }

    /**
     * Get all the models from the database
     *
     * @param array $columns
     * @return Collection
     */
    public function all(array $columns = ['*']): Collection
    {
        return $this->model::all($columns);
    }

    /**
     * Get all the models from the database with pagination
     *
     * @param array $columns
     * @param int $limit
     * @return Collection
     */
    public function paginateAll(array $columns = ['*'], int $limit = 15): Collection
    {
        return $this->model::query()->select($columns)->paginate($limit);
    }
}
