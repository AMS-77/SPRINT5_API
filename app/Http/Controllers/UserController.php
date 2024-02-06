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
        /* Validamos los datos que llegan desde la aplicación. Si algo falla 
        el framework nos dará un error.*/
        $validatedData = $request->validate([
            'name' => 'nullable|max:25|unique:users', // Si se rellena el campo 'name', debe ser único.
            'email' => 'required|email|unique:users',
            'password' => 'required', // Hacemos que la contraseña deba ser confirmada (campo password_confirmation).
            'date' => 'required|date', ]);

        // Si el usuario no ha rellenado el nombre, por defecto será 'Anónimo'.
        if(empty($validatedData['name'])) {
            $validatedData['name'] = $this->creationAnonymousName();
        }

        // Así guardamos la contraseña encriptada en la BD.
        $validatedData['password'] = Hash::make($request->password);

        // Creamos el nuevo usuario en la BD y hacemos una instancia del usuario.
        $user = User::create($validatedData);

        // Cualquier usuario automaticamente pasa a tener el rol de jugador
        $user->assignRole('player');

        // Laravel Passport nos genera un nuevo token de acceso para el usuario.
        $accessToken = $user->createToken('authToken')->accessToken;

        /*Devolvemos a la aplicación usuaria de la API los datos del usuario y 
        el token de acceso temporal.*/
        return response([ 'user' => $user, 'access_token' => $accessToken]);
    }

    private function creationAnonymousName()
    {
        // Buscamos si ya existe el usuario 'Anónimo'.
        $anonymousUser = User::where('name', 'Anónimo')->first();

        // Si no existe, establecemos el nombre como 'Anónimo'.
        if (!$anonymousUser) {
            return 'Anónimo';
        } else {
            // Si ya existe, generamos un nombre único añadiendo un número.
            //Vamos a contar cuantos usuarios 'Anónimo' existen
            $anonymousCount = User::where('name', 'like', 'Anónimo%')->count();
            //el operador like busca un patron común (Anónimo%, donde % es cualquier número de caracteres) 
            return 'Anónimo' . ($anonymousCount + 1);
        }
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
        //miraremos si está autenticado
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

        $current_user = Auth::user()->id; // Obtenemos el usuario actual._

        // Buscamos al usuario en la base de datos, haciendo la petición al Modelo
        $user = User::find($id);


        // Si no existe, devolvemos  error
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
        // Listamos todos los jugadores 
        $gamers_ranking = User::orderBy('percentage_won', 'desc')->get();

        // Devolvemos el ranking de jugadores
        return response(['gamers_ranking' => $gamers_ranking]);
    }

    //Método para tener la media del % de éxito sobre todos los jugadores
    public function playersRanking()
    {
        // Calculamos la media del % de éxito y redondeamos a 2 decimales
        $mediaSuccess = round ((User::average('percentage_won')),2);

        // Damos la media como respuesta
        return response(['media success' => $mediaSuccess]);
    }

    public function lastPlayer()
    {
        // Obtenemos el menor % de éxito y al o a los jugadores con ese porcentaje
        $lastPlayers = User::where('percentage_won', User::min('percentage_won'))->get();

        // Devolvemos el peor o peores jugadores
        return response(['worst_players' => $lastPlayers]);
    }

    public function firstPlayer()
    {
        // Obtenemos el mayor % de éxito y al o a los jugadores con ese porcentaje
        $firstPlayers = User::where('percentage_won', User::max('percentage_won'))->get();

        // Devolvemos el mejor o mejores jugadores
        return response(['worst_players' => $firstPlayers]);
    }



}


