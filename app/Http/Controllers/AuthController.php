<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;


class AuthController extends Controller
{
    public function self(Request $request)
    {
        return $request->user();
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->json()->all() , [
            'full_name' => 'required|string|max:255|min:3',
            'username' => 'required|string|max:255|min:3|unique:users',
            'email' => 'required|string|email|max:255|unique:users|min:8',
            'password' => 'required|string|min:6|max:20'
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        User::query()->create([
            'full_name' => $request->json()->get('full_name'),
            'username' => $request->json()->get('username'),
            'email' => $request->json()->get('email'),
            'password' => Hash::make($request->json()->get('password')),
        ]);

        return response()->json(['message' => 'You are successfully registered.'],201);
    }

    public function login(Request $request): JsonResponse
    {
        $valid = Validator::make($request->json()->all(),[
            "login"=> ["required","string"],
            "password" => ["required","string"]
        ]);
        $input = $request->all();

        if($valid->fails())
            return response()->json($valid->errors(),400);

        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = array($fieldType => $input['login'], 'password' => $input['password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid Credentials'], 404);
        }

        return response()->json(compact('token'));
    }
}
