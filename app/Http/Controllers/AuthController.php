<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $scope;

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255', 
            'email' => 'required|email|unique:users', 
            'password' => 'required|min:6|confirmed'
        ]);


        $user = User::create([
            'name' => $request->name, 
            'email' => $request->email, 
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken($request->email.'-'.now());

        // return response(['user' => $user, 'token' => $token]);

        return response()->json(['user' => $user, 'token' => $token->accessToken]);
    }

    public function login(Request $request){

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);
    
        if(Auth::attempt(['email'=>$request->email, 'password'=>$request->password]) ) {
    
            $user = Auth::user();
            $userRole = $user->role()->first();
    
            if ($userRole) {
                $this->scope = $userRole->role;
            }
            
            $token = $user->createToken($user->email.'-'.now(), [$this->scope]);      
    
            return response()->json(['user' => $user, 'token' => $token->accessToken]);

        } else {

            return response()->json(['error_message' => 'Incorrect Details. Please try again']);

        }

    }

}
