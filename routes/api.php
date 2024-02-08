<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
/*Referenciamos las rutas con ->name(...) por si algun dia cambiasen las urls
y asi solo las deberíamos cambiar aquí */
Route::get('/test', [UserController::class, 'test'])->name('test');
Route::post('/players', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');



Route::middleware('auth:api')->group(function () {   //Laravel/Passport
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {   //Spatie/Permissions

        Route::get('/players', [UserController::class, 'showPlayers'])->name('players.showPlayers');
        Route::get('/players/ranking', [UserController::class, 'playersExitPercentage'])->name('players.playersExitPercentage');
        Route::get('/players/ranking/loser', [UserController::class, 'lastPlayer'])->name('players.lastPlayer');
        Route::get('/players/ranking/winner', [UserController::class, 'firstPlayer'])->name('players.firstPlayer');
    });

    Route::middleware('role:player')->group(function () {   //Spatie/Permissions

        Route::put('/players/{id}', [UserController::class, 'update'])->name('players.update');
        Route::delete('/players/{id}/games', [GameController::class, 'EliminatePlayerRolls'])->name('games.EliminatePlayerRolls');
        Route::post('/players/{id}/games', [GameController::class, 'playerRollsDice'])->name('games.playerRollsDice');
        Route::get('/players/{id}/games', [GameController::class, 'listPlaysPlayer'])->name('games.listPlaysPlayer');
    });
});



