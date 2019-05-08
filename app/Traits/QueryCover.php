<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait QueryCover
{
    /**
     * Will include all relations defined in the 'cover' query string
     */

    protected function cover(Request $request, $query)
    {
        $include = $request->query('cover');

        if ($include) {
            $includeValues = explode(',', $include);
            $query->with($includeValues);
        }
    }
}
