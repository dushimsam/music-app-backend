<?php

use App\Http\Controllers\AlbumController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ==== Auth service

Route::group(["prefix" => "auth"], function () {
    Route::post("/register", [UserController::class, "register"]);
    Route::post("/login", [UserController::class, "login"]);
});


// ==== User service

Route::group(["prefix" => "user"], function () {
    Route::get("", [UserController::class, "all"]);
});


// ==== Album Service

Route::group(["prefix" => "album"], function () {
    Route::get("", [AlbumController::class, "all"]);
    Route::get("/{album}", [AlbumController::class, "show"]);
    Route::get("/songs", [AlbumController::class, "songs"]);
    Route::post("", [AlbumController::class, "create"]);
    Route::put("/{album}/upload", [AlbumController::class, "uploadImage"]);
});



// ==== Genre Service

Route::group(["prefix" => "genre"], function () {
    Route::get("", [GenreController::class, "all"]);
    Route::get("/{genre}",[GenreController::class, "show"]);
    Route::get("/songs", [GenreController::class, "songs"]);
    Route::post("", [GenreController::class, "create"]);
});

// ==== Songs Service

Route::group(["prefix" => "song"], function () {
    Route::get("", [SongController::class, "all"]);
    Route::get("/{song}", [SongController::class, "show"]);
    Route::post("", [SongController::class, "create"]);
});




