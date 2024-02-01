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
        'name' => 'nullable|unique:users|not_in=admin', // Si se rellena el campo 'name', debe ser único.
        'email' => 'required|email|unique:users|not_in:admin@admin.es',
        'password' => 'required|confirmed', // Hacemos que la contraseña deba ser confirmada (campo password_confirmation).
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
    

    //Esta función "login" trabaja el inicio de sesión del usuario.
    public function login(Request $request)
    {
    // Primero, miramos si existe el usuario admin, si no existe, crea uno asignando email y password
    $admin = User::firstOrCreate(
        ['name' => 'admin'],
        ['email' => 'admin@administrator.com', 'password' => Hash::make('admin')]);
    // Asignamos el rol admin
    if (!$admin->hasRole('admin')) {
        $admin->assignRole('admin');
    }
    
    $loginData = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

        if (!Auth::attempt($loginData)) {
            return response(['message' => 'Invalid credentials']);
        }
        $user = Auth::user();
        $token = $request->user()->createToken('token')->plainTextToken;
        //$user = auth()->user();
        //$token = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'access_token' => $token]);
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

    // Actualizamos
    $user->name = $validatedData['name'];
    $user->save();

    // Devolvemos una confirmación de que se ha actualizado
    return response(['message' => 'Name updated', 'user' => $user]);
    }


}


