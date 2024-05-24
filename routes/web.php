<?php

use App\Http\Controllers\CoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [CoreController::class, 'index'])->name('core.index');
Route::post('/process', [CoreController::class, 'process'])->name('core.process');
Route::post('/download-remaining-csv', [CoreController::class, 'downloadRemainingItems'])->name('core.remaining.items');
