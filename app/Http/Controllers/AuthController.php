<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                "status" => "success",
                "message" => "Registration successful",
                'data' => [
                    "name" => $user->name,
                    "email" => $user->email,
                    "updated_at" => $user->updated_at,
                    "created_at" => $user->created_at,
                    "id" => $user->id,
                    "token" => $token
                ]
            ], 201);
        } catch (ValidationException $error) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid field",
                'errors' => $error->errors()
            ], 422);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "status" => "error",
                "message" => "Username or password incorrect"
            ], 401);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            "status" => "success",
            "message" => "Login successful",
            "data" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at,
                "token" => $token
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            "status" => "success",
            "message" => "Logout successful"
        ], 200);
    }
}
