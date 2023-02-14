<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Song;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\HttpFoundation\File\UploadedFile;

//use Illuminate\Support\Facades\Input;

class AlbumController extends Controller
{


    public function all(): JsonResponse
    {
        $albumList = Album::orderBy('created_at', 'desc')->get();
        return response()->json($albumList);
    }

    public function allPaginated(): JsonResponse
    {
        $albumList = Album::select("*")
            ->where("status", 1)
            ->orderBy("created_at", "desc")
            ->paginate(5);

        return response()->json($albumList);
    }

    public function show(Album $album): JsonResponse
    {
        return response()->json($album);
    }

    public function songs(Album $album): JsonResponse
    {
        $songs = Song::where('album_id', $album->id)->with('genre')->paginate(10);
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
        $valid = Validator::make($request->json()->all(), [
            "cover_image_url" => "required|string"
        ]);
        if ($valid->fails()) return response()->json(['message' => Arr::first(Arr::flatten($valid->messages()->get('*')))], 400);

        Album::where('id', $album->id)->update(array('cover_image_url' => $request->json()->get("cover_image_url"), 'status' => 1));

        return response()->json(['message' => 'Image added Successfully', 'model' => Album::find($album->id)], 201);
    }


    public function update(Request $request, Album $album): JsonResponse
    {
        $valid = Validator::make($request->json()->all(), [
            "title" => "required|string|min:2|max:100|unique:albums",
            "description" => "required|string|min:3|max:200",
            "release_date" => "required|date"
        ]);

        if ($valid->fails()) return response()->json(['message' => Arr::first(Arr::flatten($valid->messages()->get('*')))], 400);


        $album = $album->update([
            "title" => $request->json()->get("title"),
            "description" => $request->json()->get("description"),
            "release_date" => $request->json()->get("release_date")
        ]);

        return response()->json(['message' => 'Updated Successfully', 'model' => $album], 201);
    }

    public function destroy(Album $album): JsonResponse
    {
        try {

            $album->songs()->delete();
            $album->delete();

            return response()->json([
                'message' => 'Album and all associated songs have been deleted'
            ]);
        } catch (Exception $e) {
            return response()->json($e->getMessage(),501);
        }
    }

}
