<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Song;
use Exception;
use GrahamCampbell\ResultType\Error;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    public function all(): JsonResponse
    {
        $genreList = Genre::orderBy('created_at', 'desc')->get();
        return response()->json($genreList);
    }


    public function allPaginated(): JsonResponse
    {
        $genreList = Genre::orderBy('created_at', 'desc')->paginate(5);
        return response()->json($genreList);
    }

    public function show(Genre $genre): JsonResponse
    {
        return response()->json($genre);
    }

    public function songs(Genre $genre): JsonResponse
    {
        $songs = Song::where('genre_id', $genre->id)->with('album')->paginate(10);
        return response()->json($songs);
    }


    public function create(Request $request): JsonResponse
    {
        $valid = Validator::make($request->json()->all(), [
            "type" => "required|string|min:3|max:100|unique:genres",
        ]);

        if ($valid->fails()) return response()->json(['message' => Arr::first(Arr::flatten($valid->messages()->get('*')))], 400);

        try {
            $genre = Genre::query()->create([
                "type" => $request->json()->get("type")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 501);
        }
        return response()->json(['message' => 'Created Successfully', 'model' => $genre], 201);
    }

    public function update(Request $request, Genre  $genre): JsonResponse
    {

        $valid = Validator::make($request->json()->all(), [
            "type" => "required|string|min:3|max:100|unique:genres",
        ]);

        if ($valid->fails()) return response()->json(['message' => Arr::first(Arr::flatten($valid->messages()->get('*')))], 400);
        $id = $genre->id;

        try {
            $genre = $genre->update([
                "type" => $request->json()->get("type")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 501);
        }
        return response()->json(['message' => 'Updated Successfully', 'model' => Genre::find($id)], 200);
    }

    public function destroy(Genre $genre): JsonResponse
    {
        try {
            $genre->songs()->delete();
            $genre->delete();

            return response()->json([
                'message' => 'Genre and all associated songs have been deleted'
            ]);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 501);
        }
    }

}
