<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait QueryWhere
{
    protected function where(Request $request, $query)
    {
        $where = $request->query('where');

        if ($where) {

            collect(explode(',', $where))
                ->map(function ($filter) {
                    return explode(':', $filter);
                })
                ->filter(function ($filter) {
                    return count($filter) == 2;
                })
                ->each(function ($filter) use ($query) {
                    $query->where($filter[0], 'like', "%$filter[1]%");
                });
        }
    }

    protected function whereBetween(Request $request, $query)
    {
        $whereBetween = $request->query('where_between');

        if ($whereBetween) {

            collect(explode(',', $whereBetween))
                ->map(function ($filter) {
                    return explode(':', $filter);
                })
                ->filter(function ($filter) {
                    return count($filter) == 3;
                })
                ->each(function ($filter) use ($query) {
                    $query
                        ->where($filter[0], '>=', $filter[1])
                        ->where($filter[0], '<=', $filter[2]);
                });
        }
    }
}
