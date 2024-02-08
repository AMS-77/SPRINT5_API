<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|max:25', 
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            // Requisitos contraseña: al menos una letra minúscula, una letra mayúscula, un número, un carácter especial y longitud mínima de 8 caracteres.
        ],
        [
            'password.required' => 'El campo de contraseña es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.regex' => 'La contraseña debe tener al menos una letra minúscula,una letra mayúscula, un número y un carácter especial (!@#$%^&*).',
            'password.confirmed' => 'La contraseña no coincide.',
        ]);

        if(empty($validatedData['name'])) {
            $validatedData['name'] = 'Anonymous';
        } else {
            // Verificamos si el nombre ya existe en la base de datos (ignorando 'Anonymous')
            $existingUser = User::where('name', $validatedData['name'])->where('name', '<>', 'Anonymous')->first();
            if($existingUser) {
                return response()->json(['error' => 'The name existings.'], 400);
            }
        }

        $validatedData['password'] = Hash::make($request->password);

        $user = User::create($validatedData);

        $user->assignRole('player');

        $accessToken = $user->createToken('authToken')->accessToken;

        return response([ 'user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required']);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'], 422);
        }
        $token = $user->createToken('authToken')->accessToken;
        return response([
            'message' => 'User ' . ucfirst($user->name) . ' logged successfully',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $token = $request->user()->token();
        
        if ($token) 
        {
            $token->revoke();
            return response()->json('Logout successful', 200);
        } else {
            return response()->json('Unauthenticated', 401);
        }
    }

    public function update(Request $request, $id)
    {

        $current_user = Auth::user()->id; // Obtenemos el usuario actual.

        // Buscamos al usuario en la base de datos, haciendo la petición al Modelo
        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        // Validamos que el nuevo nombre sea unico en la BD
        $validatedData = $request->validate([
            'name' => 'required|unique:users|max:25',]);

        if($current_user == $id) {

            // Actualizamos
            $user->name = $validatedData['name'];
            $user->save();
            // Devolvemos una confirmación de que se ha actualizado
            return response(['message' => 'Name updated', 'user' => $user]);
        }else{
            return response(['message' => 'user not Unauthorized'], 401);
        } 
    }  
    
    //Método para obtener el ranking con todos los jugadores, % en orden descendente
    public function playersExitPercentage()
    {
        $gamers_ranking = User::orderBy('percentage_won', 'desc')->get();

        return response(['gamers_ranking' => $gamers_ranking]);
    }

    //Método para tener el listado de jugadores
    public function showPlayers()
    {
        $gamers_list = User::orderBy('name', 'asc')->get();
        return response(['gamers_list' => $gamers_list]);
    }

    public function lastPlayer()
    {
        // Obtenemos el menor % de éxito y al o a los jugadores con ese porcentaje
        $lastPlayers = User::where('percentage_won', User::min('percentage_won'))->get();

        return response(['worst_players' => $lastPlayers]);
    }

    public function firstPlayer()
    {
        $firstPlayers = User::where('percentage_won', User::max('percentage_won'))->get();

        return response(['worst_players' => $firstPlayers]);
    }
}


