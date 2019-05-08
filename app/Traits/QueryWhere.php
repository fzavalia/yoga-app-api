<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait QueryWhere
{
    protected function where(Request $request, $query)
    {
        $where = $request->query('where');

        if ($where) {

            $wherePairs = explode(',', $where);

            $wherePairs = array_map(function ($pair) {
                return explode(':', $pair);
            }, $wherePairs);

            foreach ($wherePairs as $pair) {

                if (count($pair) != 2) {
                    break;
                }
                
                $query->where($pair[0], 'like', "%$pair[1]%");
            }
        }
    }
}
