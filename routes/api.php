<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware("auth:api")->get("/user", function (Request $request) {
    return $request->user();
});

makeBREAD("students", "StudentController");
makeBREAD("payments", "PaymentController", ['update']);

function makeBREAD($prefix, $controller, $ignore = [])
{
    Route::prefix($prefix)->group(function () use ($controller, $ignore) {

        $ignore = collect($ignore);

        $setPath = function ($method, $path, $call) use ($controller, $ignore) {
            if (!$ignore->contains($call)) {
                Route::{$method}($path, "$controller@$call");
            }
        };

        $setPath('post'  , ''    , 'store');
        $setPath('get'   , ''    , 'list');
        $setPath('put'   , '{id}', 'update');
        $setPath('get'   , '{id}', 'show');
        $setPath('delete', '{id}', 'delete');
    });
}
