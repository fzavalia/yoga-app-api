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
                ->map(function ($filter) {
                    return [
                        'column' => $filter[0],
                        'min' => $filter[1],
                        'max' => $filter[2]
                    ];
                })
                ->each(function ($filter) use ($query) {
                    $query
                        ->where($filter['column'], '>=', $filter['min'])
                        ->where($filter['column'], '<=', $filter['max']);
                });
        }
    }

    protected function whereRelationBetween(Request $request, $query)
    {
        $whereRelationBetween = $request->query('where_relation_between');

        if ($whereRelationBetween) {

            collect(explode(',', $whereRelationBetween))
                ->map(function ($filter) {
                    return explode(':', $filter);
                })
                ->filter(function ($filter) {
                    return count($filter) == 3;
                })
                ->map(function ($filter) {
                    $filter[0] = explode('.', $filter[0]);
                    return $filter;
                })
                ->filter(function ($filter) {
                    return count($filter[0]) == 2;
                })
                ->map(function ($filter) {
                    return [
                        'relation' => [
                            'name' => $filter[0][0],
                            'column' => $filter[0][1]
                        ],
                        'min' => $filter[1],
                        'max' => $filter[2]
                    ];
                })
                ->each(function ($filter) use ($query) {
                    $query->whereHas($filter['relation']['name'], function ($query) use ($filter) {
                        $query
                            ->where($filter['relation']['column'], '>=', $filter['min'])
                            ->where($filter['relation']['column'], '<=', $filter['max']);
                    });
                });
        }
    }
}
