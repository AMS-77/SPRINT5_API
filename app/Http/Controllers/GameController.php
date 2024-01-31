<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;

class GamesController extends Controller
{
    //Un jugador tirará los dados
    public function playerRollsDice(Request $request, $id)
    {
        // Buscamos al usuario en la base de datos
        $user = User::find($id);

        // Si el usuario no existe, devolvemos un error
        if (!$user) {
            return response(['message' => 'not exists'], 404);
        }

        // Creamos un nuevo juego
        $game = new Game;
        $game->dice1 = random_int(1, 6);
        $game->dice2 = random_int(1, 6);
        $game->game_won = ($game->dice1 + $game->dice2 == 7);//si es cierto será True

        // Asociamos el juego al usuario y lo guardamos en la base de datos
        $user->games()->save($game);

        // Devolvemos el juego como respuesta
        return response(['game' => $game], 201);
    }

    //Un jugador elimina todas sus tiradas
    public function EliminatePlayerRolls($id)
    {
        // Buscamos al usuario en la base de datos
        $user = User::find($id);

        // Si el usuario no existe, devolvemos un error
        if (!$user) {
            return response(['message' => 'not exists'], 404);
        }

        // Eliminamos todas las tiradas.
        $user->games()->delete();

        // Devolvemos un mensaje de confirmación
        return response(['message' => 'all games deleted']);
    }

    //Listar las tiradas de 1 jugador
    public function listPlaysPlayer($id)
    {
        // Buscamos al usuario en la base de datos
        $user = User::find($id);

        // Si no existe avisamos
        if (!$user) {
            return response(['message' => 'not exists'], 404);
        }

        // Obtenemos todos los juegos del usuario
        $games = $user->games;

        // Devolvemos los juegos como respuesta
        return response(['games' => $games]);
    }
}
