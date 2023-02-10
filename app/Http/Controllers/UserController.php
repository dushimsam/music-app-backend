<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json(User::all());
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user);
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->json()->all() , [
            'full_name' => 'required|string|max:255|min:3',
            'username' => 'required|string|max:255|min:3|unique:users',
            'email' => 'required|string|email|max:255|unique:users|min:8',
            'password' => 'required|string|min:6'
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        $user = User::query()->create([
            'full_name' => $request->json()->get('full_name'),
            'username' => $request->json()->get('username'),
            'email' => $request->json()->get('email'),
            'password' => Hash::make($request->json()->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public function login(Request $request): JsonResponse
    {
        $valid = Validator::make($request->json()->all(),[
            "email"=>["email","string"],
            "username"=>["string","string"],
            "password" => ["required","string","min:6"]
        ]);

        if($valid->fails())
            return response()->json($valid->errors(),400);

        $credentials = $request->json()->all();

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid Email Or Password'], 404);
        }

        return response()->json(compact('token'));
    }

    public function alubms(): JsonResponse
    {
        return response()->json(auth()->user()->alubms);
    }

}
