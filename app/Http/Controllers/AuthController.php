<?php

namespace App\Http\Controllers;

use Auth;
use Exceptions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected $scope;

    public function register(Request $request)
    {  

        $response = [];
        
        $user = User::where('email', $request['email'])->first();

        if($user){

            $response['status'] = 0;
            $response['message'] = 'Email Already Exists';
            $response['code'] = 409;

        } else {

            $request->validate([
                'name' => 'required|max:255', 
                'email' => 'required|email|unique:users', 
                'password' => 'required|min:6|confirmed'
            ]);
    
    
            $user = User::create([
                'name' => $request->name, 
                'email' => $request->email, 
                'password' => bcrypt($request->password),
                'status' => 'active'
            ]);           
            
            $response['status'] = 1;
            $response['message'] = 'User Registred Successfully';
            $response['code'] = 200;            

        }        

        return response()->json($response);

    }

    public function login(Request $request){

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        try{

            if(Auth::attempt($credentials) ) {
    
                $user = Auth::user();

                $userRole = $user->role()->first();

                if($userRole != null && $userRole->role == 'author' && $user->status != 'active') {

                    $response['status'] = 0;

                    $response['code'] = 403;

                    $response['message'] = 'User is inactive';

                    $response['data'] = null;
        
                    return response()->json($response);
                    
                }
        
                if ($userRole) {

                    $this->scope = $userRole->role;
                    
                }
                
                $token = $user->createToken($user->email.'-'.now(), [$this->scope]); 

                $data['token'] = $token->accessToken;
                
                $response['status'] = 1;

                $response['code'] = 200;

                $response['message'] = 'Login Successfully';

                $response['data'] = $data;
        
                return response()->json($response);
    
            } else {

                $response['status'] = 0;

                $response['code'] = 401;

                $response['message'] = 'Email or Password is incorrect';

                $response['data'] = null;
    
                return response()->json($response);
    
            }

        }catch(Exception $e){

            $response['data'] = null;

            $response['code'] = 500;

            $response['message'] = 'Could not create token';

            return response()->json($response);

        }     

    }

}
