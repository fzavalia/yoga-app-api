<?php

namespace App\Helpers;

use App\Traits\QueryCover;
use App\Traits\QueryOrder;
use App\Traits\QueryWhere;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class RequestHelpers
{
    use QueryCover, QueryOrder, QueryWhere;

    public static function show(Request $request, $id, Builder $query)
    {
        return (new RequestHelpers)->_show($request, $id, $query);
    }

    public static function list(Request $request, Builder $query)
    {
        return (new RequestHelpers)->_list($request, $query);
    }

    private function _show(Request $request, $id, Builder $query)
    {
        $this->cover($request, $query);

        return $query->findOrFail($id);
    }

    private function _list(Request $request, Builder $query)
    {
        $this->cover($request, $query);

        $this->where($request, $query);

        $this->order($request, $query);

        return $query->get();
    }
}
