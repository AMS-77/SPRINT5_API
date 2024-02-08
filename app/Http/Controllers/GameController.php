<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;                                              

class GameController extends Controller
{ 

    // Verificar permisos de usuario
    private function authenticationUser($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response(['message' => 'Not authorized'], 401);
        }

        if ($user->id != $id) {
            return response(['message' => 'Not permitted'], 403);
        }

        return null; 
    }

    public function playerRollsDice(Request $request, $id)
    {
        $permissionError = $this->authenticationUser($id);
        if ($permissionError) {
            return $permissionError;
        }

        $dice1 = rand(1, 6);
        $dice2 = rand(1, 6);
        $gameWon = ($dice1 + $dice2 == 7);

        Game::create([
            'user_id' => $id,
            'dice1' => $dice1,
            'dice2' => $dice2,
            'game_won' => $gameWon,
        ]);
    
        return response([
            'dice1' => $dice1,
            'dice2' => $dice2,
            'game_won' => $gameWon,
        ], 201);
    }

    public function eliminatePlayerRolls($id)
    {
        $permissionError = $this->authenticationUser($id);
        if ($permissionError) {
            return $permissionError;
        }

        Game::where('user_id', $id)->delete();

        // Actualizar el porcentaje de victorias a cero
        $user = User::find($id);
        if ($user) {
            $user->percentage_won = 0;
            $user->save();
        } else {
            return response(['message' => 'User not found'], 404);
        }

        return response(['message' => 'All games deleted']);
    }

    public function listPlaysPlayer($id)
    {
        $permissionError = $this->authenticationUser($id);
        if ($permissionError) {
            return $permissionError;
        }

        // Obtener todas las tiradas del usuario
        $games = Game::where('user_id', $id)->get();

        return response(['games' => $games]);
    }
}




