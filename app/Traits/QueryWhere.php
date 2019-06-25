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
                    $query->where($filter[0], 'ilike', "%$filter[1]%");
                });
        }
    }

    protected function whereEquals(Request $request, $query)
    {
        $whereEquals = $request->query('where_equals');

        if ($whereEquals) {

            collect(explode(',', $whereEquals))
                ->map(function ($filter) {
                    return explode(':', $filter);
                })
                ->filter(function ($filter) {
                    return count($filter) == 2;
                })
                ->each(function ($filter) use ($query) {
                    $query->where($filter[0], $filter[1]);
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

    protected function whereRelation(Request $request, $query)
    {
        $whereRelation = $request->query('where_relation');

        if ($whereRelation) {

            collect(explode(',', $whereRelation))
                ->map(function ($filter) {
                    return explode(':', $filter);
                })
                ->filter(function ($filter) {
                    return count($filter) == 2;
                })
                ->map(function ($filter) {
                    $filter[0] = explode('.', $filter[0]);
                    return $filter;
                })
                ->filter(function ($filter) {
                    return count($filter[0]) == 2;
                })
                ->each(function ($filter) use ($query) {
                    $query->whereHas($filter[0][0], function ($query) use ($filter) {
                        $query->where($filter[0][1], 'ilike', "%$filter[1]%");
                    });
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
                ->each(function ($filter) use ($query) {
                    $query->whereHas($filter[0][0], function ($query) use ($filter) {
                        $query
                            ->where($filter[0][1], '>=', $filter[1])
                            ->where($filter[0][1], '<=', $filter[2]);
                    });
                });
        }
    }
}
