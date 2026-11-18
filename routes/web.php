
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CalendarioPublicoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Página pública — Calendário de eventos
|--------------------------------------------------------------------------
| Qualquer visitante pode visualizar o calendário sem autenticação.
*/
Route::get('/', [CalendarioPublicoController::class, 'index'])
    ->name('calendario.publico');

/*
|--------------------------------------------------------------------------
| Autenticação
|--------------------------------------------------------------------------
| Apenas visitantes não autenticados acessam o formulário de login.
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');
});

/*
|--------------------------------------------------------------------------
| Área administrativa — Acesso autenticado
|--------------------------------------------------------------------------
| Dashboard, eventos e usuários permanecem protegidos.
*/
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
