<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// RETURN API
Route::get('/api/get-workspace-list', [App\Http\Controllers\WorkspaceController::class, 'getWorkspaceList']);
Route::get('/api/get-workspace-task-list', [App\Http\Controllers\TaskController::class, 'getWorkspaceTaskList']);

// RETURN VIEW
Route::get('{any}', [App\Http\Controllers\WorkspaceController::class, 'displayHome'])->where('any', '.*');

Route::post('/insert-workspace', [App\Http\Controllers\WorkspaceController::class, 'insertData']);
Route::post('/insert-workspace-task', [App\Http\Controllers\WorkspaceController::class, 'insertDataTask']);
Route::put('/update-workspace-task/{id}', [App\Http\Controllers\WorkspaceController::class, 'updateDataTask']);
