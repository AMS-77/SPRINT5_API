<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{ 
    //Un jugador tirará los dados
    public function playerRollsDice(Request $request, $id)
    {
        //Verificamos que el usuario actual está autenticado
        $user=Auth::user();
        if (!$user) {
            return response(['message' => 'not authorized'], 401);
        }

        //comparamos el ID del usuario autenticado ($user->id) con el ID puesto en la URL ($id).
        if ($user->id != $id) {
            return response(['message' => 'not permitted'], 401);
        }

        // Validamos que tengamos los 2 dados.
        if (!isset($request->dice1) || !isset($request->dice2)) {
            return response(['message' => 'The 2 dices are required'], 400);
        }  

        //Tiradas de dados y calculamos si gana o pierde
        $dice1 = rand(1,6);
        $dice2 = rand(1,6);
        $game_won  = ($dice1 + $dice2 == 7);

        // Guardamos la tirada en la BD
        Game::create([
            'user_id' => $user->id,
            'dice1' => $dice1,
            'dice2' => $dice2,
            'game_won' => $game_won,
        ]);

        // Devolvemos el juego como respuesta
        return response([
            'dice1' => $dice1,
            'dice2' => $dice2,
            'game_won' => $game_won,
        ], 201);
    }

    //Un jugador elimina todas sus tiradas
    public function EliminatePlayerRolls($id)
    {
        // Verificamos que el usuario actual esté autenticado
        $user = Auth::user();
        if (!$user) {
            return response(['message' => 'not authorized'], 401);
        }

        //comparamos el ID del usuario autenticado ($user->id) con el ID puesto en la URL ($id).
        if ($user->id != $id) {
            return response(['message' => 'not permitted'], 401);
        }

        // Eliminamos todas las tiradas  
        Game::where('user_id', $user->id)->delete();

        // Devolvemos un mensaje de confirmación
        return response(['message' => 'all games deleted']);
    }

    //Listar las tiradas de 1 jugador
    public function listPlaysPlayer($id)
    {
        // Verificamos que el usuario actual esté autenticado
        $user = Auth::user();
        if (!$user) {
            return response(['message' => 'not authorized'], 401);
        }

        //comparamos el ID del usuario autenticado ($user->id) con el ID puesto en la URL ($id).
        if ($user->id != $id) {
            return response(['message' => 'not permitted'], 401);
        }

        // Obtenemos todos los juegos del usuario
        $games = $user->games;

        // Devolvemos los juegos como respuesta
        return response(['games' => $games]);
    }
}
