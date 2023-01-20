<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
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
Route::post("register/student", [MainController::class, 'studentRegister']);


Route::post("register/teacher", [MainController::class, 'teacherRegister']);

Route::group([
    'prefix' => 'main/student'
], function ($router) {
    Route::post('login', [MainController::class,'studentLogin']);

    Route::get('approved/{id}', [MainController::class,'studentApproved']);
});

Route::group([
    'prefix' => 'main/teacher'
], function ($router) {
    Route::post('login', [MainController::class,'teacherLogin']);
    Route::get('approved/{id}', [MainController::class,'teacherApproved']);
});


