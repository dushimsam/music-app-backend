<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Genre;
use App\Models\Song;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SongController extends Controller
{
    public function all(): JsonResponse
    {
        $albumList = Song::orderBy('created_at', 'desc')->get();
        return response()->json($albumList);
    }

    public function allPaginated(): JsonResponse
    {
        $songs = Song::with('album', 'genre')->orderBy("created_at", "desc")->paginate(10);;
        return response()->json($songs);
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
            $matchThese = ['album_id' => $request->json()->get("album_id"), 'title' => $request->json()->get("title"), 'genre_id' => $request->json()->get("genre_id")];
            $duplicate = Song::where($matchThese)->get();
            if (!$duplicate->isEmpty()) {
                return response()->json(['message' => 'Song already exists'], 400);
            }

            $song = Song::query()->create([
                "title" => $request->json()->get("title"),
                "length" => $request->json()->get("length"),
                "album_id" => $request->json()->get("album_id"),
                "genre_id" => $request->json()->get("genre_id")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 501);
        }
        $album = Album::find($request->album_id);
        $genre = Genre::find($request->genre_id);

        $song->album = $album;
        $song->genre = $genre;

        return response()->json(['message' => 'Song created successfully', 'model' => $song], 201);
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

            $song->update([
                "title" => $request->json()->get("title"),
                "length" => $request->json()->get("length"),
                "album_id" => $request->json()->get("album_id"),
                "genre_id" => $request->json()->get("genre_id")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 501);
        }

        $album = Album::find($request->album_id);
        $genre = Genre::find($request->genre_id);
        $song = Song::find($song->id);
        $song->album = $album;
        $song->genre = $genre;

        return response()->json(['message' => 'Updated successfully', 'model' => $song], 200);
    }

    public function delete(Song $song): JsonResponse
    {
        try {
            $song->delete();
            return response()->json(['message' => ' Song Deleted Successfully'], 204);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 501);
        }
    }

}
