<?php

namespace App\Helpers;

use App\Traits\QueryInclude;
use App\Traits\QueryOrder;
use App\Traits\QueryWhere;
use App\Traits\QueryPaginate;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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

    /**
     * The difference with list is that it returns the query instead of the results
     */
    public static function listQuery(Request $request, Builder $query)
    {
        return (new ControllerHelpers)->_listQuery($request, $query);
    }

    public static function showForCurrentUser(Request $request, $id, Builder $query)
    {
        $item = self::show($request, $id, $query);

        if ($item->user_id != $request->user()->id) {
            abort(403, 'Resource not available for this user');
        }

        return $item;
    }

    public static function listForCurrentUser(Request $request, Builder $query)
    {
        $query->where('user_id', $request->user()->id);

        return self::list($request, $query);
    }

    /**
     * The difference with listForCurrentUser is that it returns the query instead of the results
     */
    public static function listForCurrentUserQuery(Request $request, Builder $query)
    {
        $query->where('user_id', $request->user()->id);

        return self::listQuery($request, $query);
    }

    public static function validateUserCanHandleResource(Request $request, Model $resource)
    {
        if ($request->user()->id !== $resource->user_id) {
            abort(403);
        }
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
        $this->_listQuery($request, $query);

        if ($this->paginationRequired($request)) {
            return $this->paginate($request, $query);
        }

        return $query->get();
    }

    private function _listQuery(Request $request, Builder $query)
    {
        $this->include($request, $query);

        $this->where($request, $query);

        $this->whereEquals($request, $query);

        $this->whereBetween($request, $query);

        $this->whereRelation($request, $query);

        $this->whereRelationEquals($request, $query);

        $this->whereRelationBetween($request, $query);

        $this->order($request, $query);
    }
}
