<?php

namespace App\Helpers;

use App\Traits\QueryInclude;
use App\Traits\QueryOrder;
use App\Traits\QueryWhere;
use App\Traits\QueryPaginate;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ControllerHelpers
{
    use QueryInclude, QueryOrder, QueryWhere, QueryPaginate;

    public static function show(Request $request, $id, Builder $query)
    {
        return (new ControllerHelpers)->_show($request, $id, $query);
    }

    public static function list(Request $request, Builder $query)
    {
        return (new ControllerHelpers)->_list($request, $query);
    }

    public static function showForCurrentUser(Request $request, $id, Builder $query)
    {
        return (new ControllerHelpers)->_showForCurrentUser($request, $id, $query);
    }

    public static function listForCurrentUser(Request $request, Builder $query)
    {
        return (new ControllerHelpers)->_listForCurrentUser($request, $query);
    }

    public static function jsonResponse($content, $status = 200, $headers = [])
    {
        return response($content, $status, array_merge($headers, ['Content-Type' => 'application/json']));
    }

    private function __construct()
    { }

    private function _show(Request $request, $id, Builder $query)
    {
        $this->include($request, $query);

        return $query->findOrFail($id);
    }

    private function _list(Request $request, Builder $query)
    {
        $this->include($request, $query);

        $this->where($request, $query);

        $this->whereBetween($request, $query);

        $this->whereRelation($request, $query);

        $this->whereRelationBetween($request, $query);

        $this->order($request, $query);

        if ($this->paginationRequired($request)) {
            return $this->paginate($request, $query);
        }

        return $query->get();
    }

    private function _showForCurrentUser(Request $request, $id, Builder $query)
    {
        $item = $this->_show($request, $id, $query);

        if ($item->user_id != $request->user()->id) {
            abort(403, 'Resource not available for this user');
        }

        return $item;
    }

    private function _listForCurrentUser(Request $request, Builder $query)
    {
        $query->where('user_id', $request->user()->id);

        return $this->_list($request, $query);
    }
}
