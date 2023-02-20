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

    public function register(Request $request)
    {
        // return response([
        //     'message' => 'entra en UserController::register'
        // ]);
        // die;

        if ($request->name == null || $request->name =='') {
            $fieldsetValidated = $request->validate([
                'name' => 'nullable',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required'
            ]);
            $fieldsetValidated['name'] = 'Anonymous';
        } else {
            $fieldsetValidated = $request->validate([
                'name' => 'required|max:40|unique:users,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|confirmed',
                'password_confirmation' => 'required'
            ]);
        };

        $fieldsetValidated['password'] = Hash::make($request->password);
        $user = User::create($fieldsetValidated);

        $accessToken = $user->createToken('authToken')->accessToken;
        return response([
            'message' => 'Successfully registered',
            'user' => $user, 
            'access_token' => $accessToken,
        ]);
    }

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
                'user' => auth()->user(),
                'access_token' => $accessToken,
                'status' => 200
            ]);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response([
            'user' => $request->user(),
            'message' => 'user logged out',
            'status' => 200
        ]);
    }

    public function edit(Request $request, $id_player)
    {        
        // return response([
        //     'message' => 'debug:  entra en Edit'
        // ]);
        // die;

        $idUserLoggedIn = Auth::id();
        $objUserToModif = User::find($id_player);

        if (!$objUserToModif){
            return response([
                'message' => 'user not found',
                'status' => 404,
            ]);

        } elseif($idUserLoggedIn==$id_player)
        {
            
            $request->validate([
                'name' =>'required|min:4|max:20|unique:users',
            ]);
            // $objUserBefore = $objUserToModif;
            $objUserToModif->update($request->all());
            return response([
                'message' => 'updated successfully',
                // 'user_old_data' => $objUserBefore,
                'user_new_data' => $objUserToModif,
                'status' => 200
            ]);
        }
    }
}
