<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Render this in postman, JSON object. covert from message "hello world" to response from open ai.
// Google: Laravel API structure and Laravel RESTful APIs.
// Pass something to the function to have a dynamic parameter (passing postbodies to laravel api routes)
Route::get('hello_world', function () {
    return response()->json(['message' => 'Hello World']);
});


// DONT DO ANYTHING IN MIDDLEWARE ROUTE!!!!

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

