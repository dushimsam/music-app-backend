<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use GrahamCampbell\ResultType\Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json(Genre::all());
    }

    public function show(Genre $genre): JsonResponse
    {
        $genre->songsDoc = $genre->songs()->count();
        return response()->json($genre);
    }

    public function songs(Genre $genre): JsonResponse
    {
        $songs = $genre->songs;
        return response()->json($songs);
    }

    public function create(Request $request): JsonResponse
    {
        $valid = Validator::make($request->json()->all(), [
            "type" => "required|string|min:3|max:100|unique:genres",
        ]);

        if($valid->fails()) return response()->json($valid->errors());

        $genre = Genre::query()->create([
            "type" => $request->json()->get("type")
        ]);

        return response()->json($genre);
    }
}
