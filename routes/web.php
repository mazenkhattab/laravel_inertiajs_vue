<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('users',[UserController::class,'index'])->middleware(['auth'])->name('users.index');
Route::get('user/add',[UserController::class,'create'])->middleware(['auth'])->name('users.create');
Route::post('users',[UserController::class,'store'])->middleware(['auth'])->name('users.store');
Route::get('user/{id}',[UserController::class,'edit'])->middleware(['auth'])->name('users.edit');
Route::put('user/{id}',[UserController::class,'update'])->middleware(['auth'])->name('users.update');
Route::delete('user/{id}',[UserController::class,'destroy'])->middleware(['auth'])->name('users.destroy');