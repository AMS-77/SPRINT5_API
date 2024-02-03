<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function test(){
        return response()->json([
            'message' => 'Funcionando'
        ],200);
    }
    public function register(Request $request)
    {
    /* Validamos los datos que llegan desde la aplicación. Si algo falla 
    el framework nos dará un error.*/
    $validatedData = $request->validate([
        'name' => 'nullable|unique:users', // Si se rellena el campo 'name', debe ser único.
        'email' => 'required|email|unique:users',
        'password' => 'required', // Hacemos que la contraseña deba ser confirmada (campo password_confirmation).
        'date' => 'required|date', ]);

    // Si el usuario no ha rellenado el nombre, por defecto será 'Anónimo'.
    if(empty($validatedData['name'])) {$validatedData['name'] = 'Anónimo';}

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
        // Se anula el token del usuario
        $request->user()->token()->revoke();

        // Devolvemos un aviso de que se ha cerrado la sesión.
        return response(['message' => 'Logout successful']);
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
        'name' => 'required|unique:users|max:25',
    ]);

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
    
    public function playersExitPercentage()
    {
        // Devolvemos un aviso de que se ha cerrado la sesión.
        return response(['message' => 'solo el admin accede aqui, es correcto']);
    }
    



}


