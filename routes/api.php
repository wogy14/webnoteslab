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

Route::post('register', 'API\RegisterController@register');

Route::middleware(['auth:api','cors'])->group(function () {
    Route::resource('notes',  'API\NoteController');
    Route::get('authors/note/{note}', 'API\AuthorController@getNoteAuthors');
    Route::post('authors/note/{note}', 'API\AuthorController@setAuthorByEmail');
    Route::delete('authors/{author}', 'API\AuthorController@destroy');
    Route::get('user', 'API\UserController@show');
    Route::put('user', 'API\UserController@update');
});

