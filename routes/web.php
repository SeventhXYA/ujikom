<?php

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

use App\Http\Controllers\DataController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DataController::class, 'index'])->name('/');
Route::post('/store-data', [DataController::class, 'store'])->name('store.data');
Route::post('data/update/{nipt}', [DataController::class, 'update'])->name('data.update');
Route::delete('data/delete/{nipt}', [DataController::class, 'destroy'])->name('data.delete');
Route::post('/search', [DataController::class, 'search'])->name('search.data');


// 404 for undefined routes
Route::any('/{page?}', function () {
    return View::make('pages.error.404');
})->where('page', '.*');
