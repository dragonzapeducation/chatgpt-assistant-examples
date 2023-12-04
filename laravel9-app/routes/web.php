<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'App\Http\Controllers\ChatController@index')->name('chats.index');
Route::post('/chats/create', 'App\Http\Controllers\ChatController@store')->name('chats.store');
Route::get('/chats/{chatgptConversation}', 'App\Http\Controllers\ChatController@view')->name('chats.view');
Route::post('/chats/{chatgptConversation}/messages/send', 'App\Http\Controllers\ChatController@sendMessage')->name('chats.message.send');
Route::get('/chats/{chatgptConversation}/poll', 'App\Http\Controllers\ChatController@poll')->name('chats.poll');
