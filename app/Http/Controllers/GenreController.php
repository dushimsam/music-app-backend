<?php

namespace App\Http\Controllers;

use App\Models\Genre;
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
        $genreList = Genre::orderBy('created_at', 'desc')->paginate(2);
        return response()->json($genreList);
    }

    public function show(Genre $genre): JsonResponse
    {
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

        try {
            $genre = $genre->update([
                "type" => $request->json()->get("type")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 501);
        }
        return response()->json(['message' => 'Updated Successfully', 'model' => $genre], 204);
    }

    public function delete(Genre $genre): JsonResponse
    {
        try {
            return response()->json(['message' => 'Deleted Successfully', 'model' => $genre->delete()], 204);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 501);
        }
    }

}
