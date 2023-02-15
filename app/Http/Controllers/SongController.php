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
    // Return all songs in descending order of creation time
    public function all(): JsonResponse
    {
        $albumList = Song::orderBy('created_at', 'desc')->get();
        return response()->json($albumList);
    }

    // Return paginated songs with their associated album and genre in descending order of creation time
    public function allPaginated(): JsonResponse
    {
        $songs = Song::with('album', 'genre')->orderBy("created_at", "desc")->paginate(10);;
        return response()->json($songs);
    }

    // Return a single song
    public function show(Song $song): JsonResponse
    {
        return response()->json($song);
    }

    // Create a new song
    public function create(Request $request): JsonResponse
    {
        // Validate request parameters
        $valid = Validator::make($request->json()->all(), [
            "title" => "required|string|min:3|max:100",
            "length" => "required|integer|min:1",
            "album_id" => "required|integer|min:1",
            "genre_id" => "required|integer|min:1",
        ]);

        // Return error if validation fails
        if ($valid->fails()) return response()->json(['message' => Arr::first(Arr::flatten($valid->messages()->get('*')))], 400);

        try {
            // Check if album and genre exist
            Album::query()->findOrFail($request->json()->get("album_id"));
            Genre::query()->findOrFail($request->json()->get("genre_id"));

            // Check if song already exists
            $matchThese = ['album_id' => $request->json()->get("album_id"), 'title' => $request->json()->get("title"), 'genre_id' => $request->json()->get("genre_id")];
            $duplicate = Song::where($matchThese)->get();
            if (!$duplicate->isEmpty()) {
                return response()->json(['message' => 'Song already exists'], 400);
            }

            // Create new song query
            $song = Song::query()->create([
                "title" => $request->json()->get("title"),
                "length" => $request->json()->get("length"),
                "album_id" => $request->json()->get("album_id"),
                "genre_id" => $request->json()->get("genre_id")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 501);
        }

        // Get associated album and genre and add to song
        $album = Album::find($request->album_id);
        $genre = Genre::find($request->genre_id);

        $song->album = $album;
        $song->genre = $genre;

        // Return success message and newly created song
        return response()->json(['message' => 'Song created successfully', 'model' => $song], 201);
    }

    // Update an existing song
    public function update(Request $request, Song $song): JsonResponse
    {
        // Validate request parameters
        $valid = Validator::make($request->json()->all(), [
            "title" => "required|string|min:3|max:100",
            "length" => "required|integer|min:1",
            "album_id" => "required|integer|min:1",
            "genre_id" => "required|integer|min:1",
        ]);

        // Return error if validation fails
        if ($valid->fails()) return response()->json(['message' => Arr::first(Arr::flatten($valid->messages()->get('*')))], 400);

        try {
            // Verify that the album and genre referenced in the request exist
            Album::query()->findOrFail($request->json()->get("album_id"));
            Genre::query()->findOrFail($request->json()->get("genre_id"));

            // Update the song with the request parameters
            $song->update([
                "title" => $request->json()->get("title"),
                "length" => $request->json()->get("length"),
                "album_id" => $request->json()->get("album_id"),
                "genre_id" => $request->json()->get("genre_id")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            // If an exception is thrown, return an error response
            return response()->json(['message' => $ex->getMessage()], 501);
        }

        // Retrieve the album and genre associated with the song and return the updated song
        $album = Album::find($request->album_id);
        $genre = Genre::find($request->genre_id);
        $song = Song::find($song->id);
        $song->album = $album;
        $song->genre = $genre;

        return response()->json(['message' => 'Updated successfully', 'model' => $song], 200);
    }

    // Delete an existing song
    public function delete(Song $song): JsonResponse
    {
        try {
            // Attempt to delete the song
            $song->delete();
            return response()->json(['message' => ' Song Deleted Successfully'], 200);
        } catch (Exception $e) {
            // If an exception is thrown, return an error response
            return response()->json($e->getMessage(), 501);
        }
    }

}
