<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;

class UserController extends Controller
{
    use HasApiTokens;

    // Exercice-1 ... POST /players --> crea un jugador/a. 
    //                @Params postman: nom, email, contrasenya, confirm-contrasenya
    //                @Retorna Message Ok/Nok (i graba en MySql tabla Users)
    public function register(Request $request)
    {
        if ($request->name == null || $request->name =='') {
            $fieldsetValidated = $request->validate([
                'name' => 'nullable',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required',
                'sysadmin' => 'nullable'
            ]);
            // Set default values:
            $fieldsetValidated['name'] = $fieldsetValidated['name'] ?? 'Anonim';
        } else {
            $fieldsetValidated = $request->validate([
                'name' => 'required|max:40|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required',
                'sysadmin' => 'nullable'
            ]);
        };
        // Set default values:
        $fieldsetValidated['sysadmin'] = $fieldsetValidated['sysadmin'] ?? 0;        
        $fieldsetValidated['password'] = Hash::make($request->password);
        // Create User and get his Token
        $objUser = User::create($fieldsetValidated);
        $accessToken = $objUser->createToken('authToken')->accessToken;
        return response([
            'message' => 'Successfully registered',
            // CORRECCIONES MENTORIA:  no mostrar info sensible, solo el nombre y listos.
            'user' => $objUser["name"], 
            'access_token' => $accessToken,
        ]);
    }

    // Exercice-2 ... POST /login --> login de jugador/a.
    //                @Params postman: email, contrasenya 
    //                @Retorna Bearer-Token (no graba res en MySql)
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        if(!auth()->attempt($loginData)) {
            return response([
                'message' => 'Invalid credentials',
                'status' => 401
            ]);
        }else{
            $accessToken = $request->user()->createToken('authToken')->accessToken;

            return response([
                // CORRECCIONES MENTORIA:  no mostrar info sensible, solo el nombre y listos.
                'message' => 'Welcome ' . auth()->user()["name"] . "!!",
                // 'user' => auth()->user(),
                'access_token' => $accessToken,
                'status' => 200
            ]);
        }
    }

    // Exercice-3 ... POST /logout --> desloguejar usuari jugador/a.
    //                @Params postman: només el Bearer-Token que ens ha proporcionat el Login.
    //                @Retorna Message Ok/Nok
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response([
            // CORRECCIONES MENTORIA:  no mostrar info del usuario al hacer Logout
            // 'user' => $request->user(),
            'message' => 'user logged out',
            'status' => 200
        ]);
    }

    // Exercice-4 ... PUT /players/{id} --> modifica el nom del jugador/a.
    //                @Params postman: name i mail a modificar, i Bearer-Token per demostrar qui és.
    //                @Retorna Message Ok/Nok (i graba en MySql tabla Users)
    public function edit(Request $request, $id_player )
    {        
        $idUserLoggedIn = Auth::id(); 
        $objUserToModif = User::find($id_player); 

        if (!$objUserToModif){
            return response([
                'message' => 'user to modif not found',
                'status' => 404,
            ]);

        } elseif($idUserLoggedIn == $id_player)
        {
            
            $request->validate([
                'name' => 'required|min:4|max:20|unique:users',
            ]);

            // $objUserBefore = $objUserToModif;
            $objUserToModif->update($request->all()); 
            return response([
                'message' => 'user updated successfully',
                // 'user_old_data' => $objUserBefore,
                'user_new_data' => $objUserToModif, 
                'status' => 200 
            ]);
        }
    }
}

?>
