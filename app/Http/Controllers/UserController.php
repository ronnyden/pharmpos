<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Helper\QuestionHelper;

class UserController extends Controller
{
   public function register(Request $req){
      DB::beginTransaction();
      try{
       $req->validate([
        'first_name'=>'required|string',
        'last_name'=>'required|string',
        'email'=>'unique:users|required|email',
        'password'=>'required|min:8'
       ]);
        $user = User::create([
            'first_name'=>$req->first_name,
            'last_name'=>$req->last_name,
            'email'=>$req->email,
            'password'=>Hash::make($req->password)
          ]);
          return response()->json($user);

      }catch(QueryException $ex){
        DB::rollBack();
        return response()->json([
            'message'=>"Something went wrong while processing the request",
            'statusCode'=>500
        ]);

      }

   }
  public function updateUser(Request $req,$id){
        $user = User::find($id);
      if($user){
         try{
           $user->update([
             'first_name'=>$req->first_name,
             'last_name'=>$req->last_name,
             'email'=>$req->email,
       ]);
    }catch(QueryException $ex){
        DB::rollBack();
        return response()->json([
            'message'=>'Something went wrong while processing request',
            'statusCode'=>500
        ]);
    }
   }
}
   public function deleteUser($id){
      $user = User::find($id);
      $user->delete();
    return response("User Deleted");
}

   public function login(Request $req){
    $user  = User::where('email',$req->email)->get()->first();
    if($user){
        if($req->password == $user->password){
               return response("Login successfull");
        }
    }else{
        return response()->json([
            'message'=>"Wrong cridentials.Check and try again",
            'statusCode'=>304
        ]);
    }
   }
}
