
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Página inicial
Route::get('/', fn () => redirect()->route('dashboard'));

// Rotas públicas para visitantes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');
});

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::resource('eventos', EventoController::class)
        ->except(['show']);

    Route::resource('users', UserController::class)
        ->except(['show']);
});
