<?php

namespace App\Http\Controllers;

use auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterApiController extends Controller
{
    public function register(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        //dd($request->all());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return \response()->json($user, 200);
    }

    public function logout()
    {
        auth()->user()->tokens->each(function($token, $key){
            $token->delete();
        });

        return response()->json('logout', 200);
    }
}
