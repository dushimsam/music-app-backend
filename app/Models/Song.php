<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;
    protected $fillable = ["title","length","album_id", "genre_id"];
    public function alubm(){
        return $this->belongsTo(Album::class);
    }
    public function genre(){
        return $this->belongsTo(Genre::class);
    }
}
