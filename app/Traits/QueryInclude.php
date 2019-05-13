<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait QueryInclude
{
    /**
     * Will include all relations defined in the 'cover' query string
     */

    protected function include(Request $request, $query)
    {
        $include = $request->query('include');

        if ($include) {
            $includeValues = explode(',', $include);
            $query->with($includeValues);
        }
    }
}
