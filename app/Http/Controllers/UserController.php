<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;


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

}
