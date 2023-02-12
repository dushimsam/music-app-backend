<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Genre;
use App\Models\Song;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SongController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json(Song::all());
    }

    public function show(Song $song): JsonResponse
    {
        return response()->json($song);
    }

    public function create(Request $request): JsonResponse
    {
        $valid = Validator::make($request->json()->all(), [
            "title" => "required|string|min:3|max:100",
            "length" => "required|integer",
            "album_id" => "required|string|min:3|max:100",
            "genre_id" => "required|string|min:3|max:100",
        ]);

        if($valid->fails()) return response()->json($valid->errors());

        Album::query()->findOrFail($request->json()->get("alubm_id"));
        Genre::query()->findOrFail($request->json()->get("genre_id"));

        $song = Song::query()->create([
            "title" => $request->json()->get("title"),
            "length" => $request->json()->get("length"),
            "album_id" => $request->json()->get("album_id"),
            "genre_id" => $request->json()->get("genre_id")
        ]);

        return response()->json($song);
    }
}
