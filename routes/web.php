<?php

use App\Http\Controllers\MyAccessController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::post('layout-mode', function () {
    $mode = request('mode') === 'header' ? 'header' : 'sidebar';

    return back()->withCookie(cookie()->forever('layout_mode', $mode));
})->middleware('auth')->name('layout-mode.toggle');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('my-access', MyAccessController::class)->name('my-access');
});

require __DIR__.'/settings.php';
