<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login', 'App\Http\Controllers\AuthController@login');

Route::post('register', 'App\Http\Controllers\AuthController@register');

Route::get('/search-books', 'App\Http\Controllers\BookController@searchBooks');

Route::middleware(['auth:api', 'role'])->group(function() {  

    Route::middleware(['scope:author'])->group(function() {

        Route::apiResource('/book', 'App\Http\Controllers\BookController');

    });

    Route::middleware(['scope:admin'])->group(function() {

        Route::patch('/changeAuthorStatus/{user}', 'App\Http\Controllers\UserController@changeAuthorStatus');

        Route::get('/authors-books', 'App\Http\Controllers\UserController@authors');

    });

    Route::get('user/{userId}/detail', 'App\Http\Controllers\UserController@show');

    // List all the users
    Route::middleware(['scope:admin,author,basic'])->get('/users', function (Request $request) {

        return User::get();

    });

    // Add/Edit User
    Route::middleware(['scope:admin,author'])->post('/user', function(Request $request) {

        return User::create($request->all());

    });

    Route::middleware(['scope:admin,author'])->put('/user/{userId}', function(Request $request, $userId) {

        try {
            $user = User::findOrFail($userId);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.'
            ], 403);
        }

        $user->update($request->all());

        return response()->json(['message'=>'User updated successfully.']);
    });

    // Delete User
    Route::middleware(['scope:admin'])->delete('/user/{userId}', function(Request $request, $userId) {

        try {
            $user = User::findOfFail($userId);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.'
            ], 403);
        }

        $user->delete();

        return response()->json(['message'=>'User deleted successfully.']);
    });

});


