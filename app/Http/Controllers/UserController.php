<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request, $userId)
    {
        $user = User::find($userId);

        if($user) {
            return response()->json($user);
        }

        return response()->json(['message' => 'User not found!'], 404);
    }

    public function changeAuthorStatus(User $user){

        if($user->status === "active"){ 

            $user->status = "inactive";

        } else {

            $user->status = "active";

        }
        

        $user->save();

        return response(['message' => 'Change author status successfully'], 200);

    }

    public function authors(){

        $authors = User::whereIn('id', function($query){

            $query->select('user_id')->from(with(new Role)->getTable())->where('role','=','author');

        })->with(['books'])->where('status','=','active')->get(); 
        
        return response(['authors' => $authors]);

    }
}
