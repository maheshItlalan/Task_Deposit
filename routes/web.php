<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Depositcontroller;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/import', [ImportController::class, 'importfile'])->name('import');
    Route::post('/import', [ImportController::class, 'import'])->name('import');
    Route::get('/deposit', [Depositcontroller::class, 'index'])->name('deposits.index');
    Route::get('/depositFail', [Depositcontroller::class, 'failedDeposits'])->name('depositFail.failed');
});

require __DIR__.'/auth.php';
