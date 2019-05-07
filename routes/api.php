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
makeBREAD("payments", "PaymentController");

function makeBREAD($prefix, $controller)
{
    Route::prefix($prefix)->group(function () use ($controller) {
        Route::post  ("",     "{$controller}@store");
        Route::get   ("",     "{$controller}@list");
        Route::put   ("{id}", "{$controller}@update");
        Route::get   ("{id}", "${controller}@show");
        Route::delete("{id}", "${controller}@delete");
    });
}
