<?php

use App\Models\admin;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
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

Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

 
Route::get('/auth/redirect', function () {
    return Socialite::driver('google')->redirect();
})->name('google');
 
Route::get('/google/callback', function () {
    $googleuser = Socialite::driver('google')->user();
    // dd($googleuser->name);
    $user = admin::updateOrCreate([
        'name' => $googleuser->name,
        'email' => $googleuser->email,
        
    ]);
 
    Auth::login($user);
 
    return redirect('/home');
 
    // $user->token
});