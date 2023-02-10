<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed alubm
 * @property mixed genre
 */
class Song extends Model
{
    use HasFactory;
    protected $fillable = ["title","length","album_id","genre_id"];

    public function alubm(){
        return $this->belongsTo(Alubm::class);
    }

    public function genre(){
        return $this->belongsTo(Genre::class);
    }
}
