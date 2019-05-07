<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait QueryWhere
{
    protected function where(Request $request, $query, $columns)
    {
        $columns = collect(['name', 'email', 'phone_number', 'dni']);

        foreach ($request->query() as $key => $value) {
            if ($columns->contains($key)) {
                $query->where($key, 'like', "%$value%");
            }
        }
    }
}
