<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SearchTrait
{
    public function scopeSearchByColumnName(Builder $query, array $columnNames, ?string $searchValue)
    {
        $splitValue = explode(" ", $searchValue);

        foreach ($columnNames as $key => $columnName) {
            $currentSearchValue = isset($splitValue[$key]) ? $splitValue[$key] : $splitValue[0];
            $queryArgs = [
                [$columnName, 'LIKE', '%' . $currentSearchValue . '%']
            ];
            if (count($splitValue) === 1) {
                $query->orWhere($queryArgs);
            } else {
                $query->where($queryArgs);
            }
        }
    }
}
