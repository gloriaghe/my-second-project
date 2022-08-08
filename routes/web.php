<?php

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

Route::get('/', function () {
    return view('guests.home');
})->name('home');

Auth::routes();

Route::middleware('auth')
->namespace('Admin')
->name('admin.')
->prefix('admin')
->group(function () {
    Route::get('/', 'AdminController@dashboard')->name('dashboard');
    Route::get('users', 'UserController@index')->name('users.index');
    Route::get('my-posts', 'PostController@myIndex')->name('posts.myIndex');
    Route::resource('posts', 'PostController');
});
