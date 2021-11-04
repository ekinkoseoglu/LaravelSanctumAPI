<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request; // We are going to get request as a email-password
use Illuminate\Http\Responses; // And we are going to give some informations as a response
use Illuminate\Support\Facades\Hash; // We need to hash the passwords that we get from request

class AuthController extends Controller
{
    // 
    public function register(Request $request)
    {
        $fields = $request->validate([ // Take these inputs as a parameter of $fields variable
            'name' => "required|string",
            'email' => "required|string|unique:users,email",
            'password' => "required|string|confirmed"
        ]);
        $user = User::create([ // Use these $fields parameters for creating new User (Register User) and Match them
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']) // Encrypts the password that 
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken; // Create the token for created user above

        $response = [ // Give response as a user
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return [
            'Message' => 'Logged Out From System'
        ];
    }
    public function login(Request $request)
    {
        $fields = $request->validate([ // Take these inputs as a parameter of $fields variable

            'email' => "required|string",
            'password' => "required|string"
        ]);

        // Check Email
        $user = User::where('email', $fields['email'])->first();

        // Check Password
        if (!$user || !Hash::check($fields['password'], $user->password)) { // If one of 2 condition is not true
            return response([
                'message' => "Bad creds"
            ], 401);
        }


        $token = $user->createToken('myapptoken')->plainTextToken; // Create the token for created user above

        $response = [ // Give response as a user
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
}
