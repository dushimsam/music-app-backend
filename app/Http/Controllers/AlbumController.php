<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        if($valid->fails()) return response()->json($valid->errors());

        $alubm = Album::query()->create([
            "user_id" => $request->json()->get("user_id"),
            "title" => $request->json()->get("title"),
            "description" => $request->json()->get("description"),
            "release_date" => $request->json()->get("release_date")
        ]);

        return response()->json($alubm);
    }

    public function uploadImage(Request $request, Album $album)
    {
//        $this->validate($request, [
//            'cover_image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
//        ]);

        if ($request->file('image') == null) {
            $file = "";
        }else{
            $file = $request->file('image')->store('images');
        }
//        $image_path = $request->file('image')->store('public/images/');

        $album = $album->update([
            "cover_image_url" =>  $file,
            "status" => 1
        ]);

        return response()->json($album);
    }

}
