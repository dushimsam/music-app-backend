<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class AlbumController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json(Album::all());
    }

    public function show(Album $album): JsonResponse
    {
        $album->songsDoc = $album->songs()->count();
        return response()->json($album);
    }

    public function songs(Album $album): JsonResponse
    {
        $songs = $album->songs;
        return response()->json($songs);
    }

    public function create(Request $request): JsonResponse
    {
        $valid = Validator::make($request->json()->all(), [
            "title" => "required|string|min:2|max:100|unique:albums",
            "description" => "required|string|min:3|max:200",
            "release_date" => "required|date"
        ]);

        if ($valid->fails()) return response()->json(['message' => Arr::first(Arr::flatten($valid->messages()->get('*')))], 400);

        try {
            $alubm = Album::query()->create([
                "title" => $request->json()->get("title"),
                "description" => $request->json()->get("description"),
                "release_date" => $request->json()->get("release_date")
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['message' => $ex->getMessage()], 501);
        }

        return response()->json(['message' => 'Created Successfully', 'model' => $alubm], 201);
    }

    public function uploadImage(Request $request, Album $album)
    {
//        $this->validate($request, [
//            'cover_image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
//        ]);

        if ($request->file('image') == null) {
            $file = "";
        } else {
            $file = $request->file('image')->store('images');
        }
//        $image_path = $request->file('image')->store('public/images/');

        $album = $album->update([
            "cover_image_url" => $file,
            "status" => 1
        ]);

        return response()->json($album);
    }

}
