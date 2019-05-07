<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait QueryOrder
{
    protected function order(Request $request, $query)
    {
        $orderBy = $request->query('order_by');

        if ($orderBy) {
            $orderType = $request->query('order_type') ?? 'desc';
            $query->orderBy($request->query('order_by'), $orderType);
        }
    }
}
