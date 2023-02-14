<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Genre;
use App\Models\Song;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class SongController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json(Song::all()->orderBy("created_at", "desc"));
    }

    public function allPaginated(): JsonResponse
    {
        $songList = Song::select("*")
            ->orderBy("created_at", "desc")
            ->paginate(10);

        return response()->json($songList);
    }

    public function show(Song $song): JsonResponse
    {
        return response()->json($song);
    }

    public function create(Request $request): JsonResponse
    {
        $valid = Validator::make($request->json()->all(), [
            "title" => "required|string|min:3|max:100",
            "length" => "required|integer|min:1",
            "album_id" => "required|integer|min:1",
            "genre_id" => "required|integer|min:1",
        ]);

        if ($valid->fails()) return response()->json(['message' => Arr::first(Arr::flatten($valid->messages()->get('*')))], 400);

        try {
            Album::query()->findOrFail($request->json()->get("album_id"));
            Genre::query()->findOrFail($request->json()->get("genre_id"));

            $song = Song::query()->create([
                "title" => $request->json()->get("title"),
                "length" => $request->json()->get("length"),
                "album_id" => $request->json()->get("album_id"),
                "genre_id" => $request->json()->get("genre_id")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 501);
        }

        return response()->json(['message' => 'Song created successfully', 'model' => $song]);
    }


    public function update(Request $request, Song $song): JsonResponse
    {
        $valid = Validator::make($request->json()->all(), [
            "title" => "required|string|min:3|max:100",
            "length" => "required|integer|min:1",
            "album_id" => "required|integer|min:1",
            "genre_id" => "required|integer|min:1",
        ]);

        if ($valid->fails()) return response()->json(['message' => Arr::first(Arr::flatten($valid->messages()->get('*')))], 400);

        try {
            Album::query()->findOrFail($request->json()->get("album_id"));
            Genre::query()->findOrFail($request->json()->get("genre_id"));

            $song = $song->update([
                "title" => $request->json()->get("title"),
                "length" => $request->json()->get("length"),
                "album_id" => $request->json()->get("album_id"),
                "genre_id" => $request->json()->get("genre_id")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 501);
        }

        return response()->json(['message' => 'Updated successfully', 'model' => $song], 204);
    }

    public function delete(Song $song): JsonResponse
    {
        try {
            return response()->json(['message' => 'Deleted Successfully', 'model' => $song->delete()], 204);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 501);
        }
    }

}
