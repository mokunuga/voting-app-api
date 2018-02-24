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

//Post

Route::get('post/{post}', 'PostController@show');
Route::get('posts', 'PostController@index');

Route::group(['middleware' => ['ability:admin,']], function() {
    Route::post('create-post', 'PostController@store');
    Route::delete('delete-post/{post}', 'PostController@destroy');
    Route::put('update-post/{post}', 'PostController@update');
});

//Candidate

Route::resource('candidates', 'CandidateController');
Route::get('candidate-by-post/{id}', 'CandidateController@candidateByPost');

Route::group(['middleware' => ['ability:admin,']], function() {
    Route::post('create-candidate', 'CandidateController@store');
    Route::delete('delete-candidate/{candidate}', 'CandidateController@destroy');
    Route::put('update-candidate/{candidate}', 'CandidateController@update');
});

//User
Route::get('logout', 'AuthController@logout');
Route::post('register', 'AuthController@register')->name('register');
Route::post('login', 'AuthController@login')->name('login');
Route::post('refresh', 'AuthController@refresh')->name('refresh');
Route::post('me', 'AuthController@me')->name('me');

//Vote
Route::group(['middleware' => ['ability:user,']], function() {
    Route::post('create-vote', 'VoteController@store');
    Route::get('user-voted/{id}', 'VoteController@hasUserVoted');
});

Route::group(['middleware' => ['ability:admin,']], function() {
    Route::get('votes', 'VoteController@getTotalVoteCount');
});



Route::group(['middleware' => ['ability:admin,','cors']], function()
{
    Route::get('users', 'AuthController@index');
});

Route::get('current-user', 'AuthController@me');

//Others
// Route to create a new role
Route::post('role', 'AuthController@createRole');
// Route to create a new permission
Route::post('permission', 'AuthController@createPermission');
// Route to assign role to user
Route::post('assign-role', 'AuthController@assignRole');
// Route to attache permission to a role
Route::post('attach-permission', 'AuthController@attachPermission');

// API route group that we need to protect


Route::middleware('jwt.refresh')->get('test', 'AuthController@test');
Route::group(['middleware' => [ 'ability:user|admin,null']], function()
{
    // Protected route
    Route::get('test2', 'AuthController@test2');
});