<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RoutingHelpers
{
    public static function makeBREAD($prefix, $controller, $ignore = [])
    {
        Route::prefix($prefix)->group(function () use ($controller, $ignore) {

            $ignore = collect($ignore);

            $setPath = function ($method, $path, $call) use ($controller, $ignore) {
                if (!$ignore->contains($call)) {
                    Route::{$method}($path, "$controller@$call");
                }
            };

            $setPath('post', '', 'store');
            $setPath('get', '', 'list');
            $setPath('put', '{id}', 'update');
            $setPath('get', '{id}', 'show');
            $setPath('delete', '{id}', 'delete');
        });
    }

    private function __construct()
    { }
}
