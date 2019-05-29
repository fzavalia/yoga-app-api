<?php

use Illuminate\Http\Request;
use App\Helpers\RoutingHelpers;

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

RoutingHelpers::makeBREAD("students", "StudentController");
RoutingHelpers::makeBREAD("payments", "PaymentController");
RoutingHelpers::makeBREAD("yoga_classes", "YogaClassController");

Route::get('/assistance_tables/{date}', "AssistanceTableController");