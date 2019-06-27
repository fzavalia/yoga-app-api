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

Route::post('/auth/register', 'AuthController@register');
Route::post('/auth/login', 'AuthController@login');

Route::middleware("auth:api")->group(function () {

    // User

    Route::get("/user", function (Request $request) {
        return $request->user();
    });

    // Students

    RoutingHelpers::makeBREAD("students", "StudentController");

    // Payments

    Route::get('/payments/total', "PaymentController@total");
    RoutingHelpers::makeBREAD("payments", "PaymentController");

    // Yoga Classes

    RoutingHelpers::makeBREAD("yoga_classes", "YogaClassController");

    // Assistance Table

    Route::get('/assistance_tables/{date}', "AssistanceTableController@show");
    Route::put('/assistance_tables/yoga_classes/{date}', "AssistanceTableController@updateYogaClass");
    Route::put('/assistance_tables/yoga_classes/{date}/students/{id}', "AssistanceTableController@updateStudentAssistance");

    // Payments Table

    Route::get('/payments_table/{date}', "PaymentsTableController");
});
