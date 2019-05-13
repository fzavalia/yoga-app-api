<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait QueryPaginate
{
    protected function paginate(Request $request, $query)
    {
        $perPage = $request->query('per_page') ?? 12;

        $paginationType = $request->query('pagination_type');

        if ($paginationType == 'simple') {
            return $query->simplePaginate($perPage);
        }

        return $query->paginate($perPage);
    }

    protected function paginationRequired(Request $request)
    {
        return !empty($request->query('page'));
    }
}
