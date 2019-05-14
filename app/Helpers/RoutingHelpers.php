<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RoutingHelpers
{
    public static function makeBREAD($prefix, $controller, $ignore = [])
    {
        Route::prefix($prefix)->group(function () use ($controller, $ignore) {

            $ignore = collect($ignore);

            $addRoutePath = function ($method, $path, $call) use ($controller, $ignore) {
                if (!$ignore->contains($call)) {
                    Route::{$method}($path, "$controller@$call");
                }
            };

            $addRoutePath('post', '', 'store');
            $addRoutePath('get', '', 'list');
            $addRoutePath('put', '{id}', 'update');
            $addRoutePath('get', '{id}', 'show');
            $addRoutePath('delete', '{id}', 'delete');
        });
    }

    private function __construct()
    { }
}
