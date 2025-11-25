<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth')->group(function () {

    Route::get('/', [ContactController::class, 'index'])->name('index');

    Route::get('/complete', [ContactController::class, 'complete'])->name('complete');

    Route::post('/contacts/confirm', [ContactController::class, 'confirm'])->name('confirm');

    Route::post('contacts/back', [ContactController::class, 'back'])->name('back');

    Route::post('/contacts', [ContactController::class, 'store'])->name('store');

    // ★修正★ historyルートを contact/history に変更し、名前を contact.history に統一します。
    Route::get('/contact/history', [ContactController::class, 'history'])->name('contact.history');

    Route::get('/contact/{contact}/edit', [ContactController::class, 'edit'])->name('contact.edit');

    // ★修正★ PATCHからPUTに変更し、edit.blade.phpと統一します。
    Route::put('/contact/{contact}', [ContactController::class, 'update'])->name('contact.update');

    Route::patch('/contact/{contact}', [ContactController::class, 'update'])->name('contact.update');

    Route::delete('/contact/{contact}', [ContactController::class, 'destroy'])->name('contact.destroy');

});


Route::get('/login', [AuthController::class, 'login'])->name('login');

Route::get('/register', [AuthController::class, 'register'])->name('register');
