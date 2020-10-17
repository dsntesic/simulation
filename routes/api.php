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
//Route for game
Route::get('/game/lists', 'GamesController@lists')->name('lists');
Route::get('/game/get', 'GamesController@get')->name('get-game');
Route::get('/game/create', 'GamesController@create')->name('create-game');
//Route for army
Route::get('/army/create', 'ArmiesController@create')->name('create-army');
Route::get('/army/attack', 'ArmiesController@attack')->name('attack');
Route::get('/army/autorun', 'ArmiesController@autorun')->name('autorun');
