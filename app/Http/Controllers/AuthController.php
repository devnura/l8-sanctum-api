<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            if ($request->wantsJson()) {
                $fields = $request->all();
                $validator = Validator::make(
                    $fields,
                    [
                        "name" => "required|string",
                        "email" => "required|string|unique:users,email",
                        "password" => "required|string|confirmed"
                    ]
                );

                if (!$validator->fails()) {
                    $user =  User::create([
                        'name' => $fields['name'],
                        'email' => $fields['email'],
                        'password' => bcrypt($fields['password']),
                    ]);
                    $token = $user->createToken('myapptoken')->plainTextToken;

                    return response([
                        "code_status" => 1,
                        "message" => "User Created",
                        "data" => [
                            'user' => $user,
                            'token' => $token
                        ]
                    ], 201);
                } else {
                    return response([
                        "code_status" => 0,
                        "message" => "Invalid Request",
                        "data" => $validator->errors()
                    ], 422);
                }
            } else {
                return response([
                    "code_status" => 0,
                    "message" => "Request must be JSON format!",
                    "data" => []
                ], 403);
            }
            // $fields = $request->all();

        } catch (\Throwable $th) {
            return response([
                'code_status' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            if ($request->wantsJson()) {
                $fields = $request->all();
                $validator = Validator::make(
                    $fields,
                    [
                        "email" => "required|email",
                        "password" => "required|string"
                    ]
                );

                if (!$validator->fails()) {

                    $user =  User::where(['email' => $fields['email']])->first();

                    if ($user && Hash::check($fields['password'], $user['password'])) {

                        $token = $user->createToken('myapptoken')->plainTextToken;

                        return response([
                            "code_status" => 1,
                            "message" => "Login Success",
                            "data" => [
                                'user' => $user,
                                'token' => $token
                            ]
                        ], 200);
                    } else {
                        return response([
                            "code_status" => 0,
                            "message" => "Invalid username or password",
                            "data" => []
                        ], 401);
                    }
                } else {
                    return response([
                        "code_status" => 0,
                        "message" => "Invalid Request",
                        "data" => $validator->errors()
                    ], 422);
                }
            } else {
                return response([
                    "code_status" => 0,
                    "message" => "Request must be JSON format!",
                    "data" => []
                ], 403);
            }
            // $fields = $request->all();

        } catch (\Throwable $th) {
            return response([
                'code_status' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            auth()->user()->tokens()->delete();
            return response([
                "code_status" => 1,
                "message" => "Logout Succes!",
                "data" => []
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'code_status' => $th->getCode(),
                'message' => $th->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
