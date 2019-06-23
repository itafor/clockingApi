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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('create-student','StudentController@createStudents');
// Route::post('clock-student','StudentController@clockStudent');
Route::get('get-student/{id}','StudentController@fetchStudent');
Route::get('clock-student/{id}','StudentController@clockStudent');
Route::get('unclock-student/{id}','StudentController@UnclockStudent');

Route::get('all-students','StudentController@allStudents');
Route::get('all-clocked-students','StudentController@clockedStudents');
Route::get('all-unclocked-students','StudentController@allunclockedStudents');