<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function create(Request $request){
        $rules = [
            "name"=>'required|string|max:100',
            "email"=>'required|string|email|max:100|unique:users',
            "password"=>'required|string|min:8'
        ];
        $validator = \Validator::make($request->input(),$rules);
        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors()->all()
            ],400);
        }

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> Hash::make($request->password)
        ]);
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('API TOKEN')->plainTextToken
        ]);
    }

    public function login(Request $request){
        $rules = [
            "email"=>'required|string|email',
            "password"=>'required|string'
        ];
        $validator = \Validator::make($request->input(),$rules);
        if($validator->fails()){
            return response()->json([
                'error'=>$validator->errors()->all()
            ],400);
        }

        if (!Auth::attempt($request->only('email','password'))) {
            return response()->json([
                'error'=> "Credenciales incorrectas"
            ],400);
        }

        $user = User::where('email',$request->email)->first();

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('API TOKEN')->plainTextToken
        ]);
    }

    public function logout(Request $request){
        
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json($user);
    }
}
